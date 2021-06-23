<?php

namespace Waboot\inc;

/**
 * Check whether the product is a bundle
 *
 * @param int|\WC_Product $product
 * @return bool
 */
function isBundleProduct($product): bool {
    if(\is_int($product)){
        $product = wc_get_product($product);
        return is_object($product) && $product->is_type('bundle');
    }
    if($product instanceof \WC_Product) {
        return $product->is_type('bundle');
    }
    return false;
}

/**
 * Get all products ids in a bundle
 *
 * @param int $bundleId
 * @return array
 */
function getBundledProductIds(int $bundleId): array {
    $productIds = get_post_meta($bundleId,'_children', true);
    if(!\is_array($productIds)){
        global $wpdb;
        $r = $wpdb->get_results('SELECT product_id FROM '.$wpdb->prefix.'woocommerce_bundled_items WHERE bundle_id = "'.$bundleId.'"');
        if(\is_array($r)){
            $productIds = array_map('intval',wp_list_pluck($r,'product_id'));
        }
    }
    return $productIds;
}

/**
 * Check if a product is in a specific bundle
 *
 * @param int $productId
 * @param int $bundleId
 * @return bool
 */
function isBundledIn(int $productId, int $bundleId): bool {
    $bundledProductIds = getBundledProductIds($bundleId);
    $pType = get_post_type($productId);
    if($pType === 'product_variation'){
        $variation = wc_get_product($productId);
        $parentId = $variation->get_parent_id();
        return \in_array($parentId,$bundledProductIds, true);
    }
    return \in_array($productId,$bundledProductIds, true);
}

/**
 * Get the current regular price of a product associated to the $orderItem
 *
 * @param \WC_Order_Item_Product $orderItem
 * @return float|int|string
 */
function getProductRegularPriceFromOrderItemProduct(\WC_Order_Item_Product $orderItem) {
    $product = $orderItem->get_product();
    $price = 0;
    if($product instanceof \WC_Product){
        $price = $product->get_regular_price();
    }
    return $price;
}

/**
 * Get the percentage value of the sale price in relation with the regular price
 *
 * @param \WC_Product $product
 * @param bool $round whether round the percentage or not
 * @param int $roundPrecision the round precision
 * @param int $additionalRound an additional round precision to apply to the percentage, for example if you want 23% to become 25% or 20%
 * @param bool $returnInteger whether return an integer or a float
 * @return float|int
 */
function getProductSalePercentage(\WC_Product $product, $round = true, $roundPrecision = 0, $additionalRound = 5, $returnInteger = true) {
    $percentage = 0;
    if(!$product->is_on_sale()){
        return 0;
    }
    if ($product->get_type() === 'variable') {
        $variations = $product->get_available_variations();
        $percentage = 0;
        $percentageArr = [];
        foreach ($variations as $variation) {
            $id = $variation['variation_id'];

            /** @var WC_Product_Variation $_product */
            $_product = new \WC_Product_Variation($id);

            if (!is_numeric($_product->is_on_sale()) && $_product->is_on_sale()) {
                $percentage = round((($_product->get_regular_price() - $_product->get_sale_price()) / $_product->get_regular_price()) * 100);
                $percentageArr[] = $percentage;
            }
        }
        if (!empty($percentageArr)) {
            $percentage = max($percentageArr);
        }
    } else {
        $regularPrice = $product->get_regular_price();
        $salePrice = $product->get_sale_price();
        $percentage = (($regularPrice - $salePrice) / $regularPrice) * 100;
    }
    if($percentage && $percentage !== 0){
        if($round){
            $percentage = round($percentage, $roundPrecision);
            if(\is_int($additionalRound) && $additionalRound !== 0){
                $percentage = round($percentage / $additionalRound) * $additionalRound;
            }
        }
        return $returnInteger ? (int) $percentage : (float) $percentage;
    }
    return $returnInteger ? (int) $percentage : (float) $percentage;
}

/**
 * Get a custom field from the provided product. If the product is a variation and doesn't have the field,
 * the parent will be used as source.
 *
 * @param \WC_Product $product
 * @param string $fieldKey
 * @param string $default
 * @return string
 */
function getHierarchicalCustomFieldFromProduct(\WC_Product $product, string $fieldKey, string $default): string {
    if(!method_exists($product,'get_id')){
        return $default;
    }
    $productId = $product->get_id();
    $fieldValue = get_post_meta($productId,$fieldKey, true);
    if((!\is_string($fieldValue) || $fieldValue === '') && $product instanceof \WC_Product_Variation){
        $parentId = $product->get_parent_id();
        $fieldValue = get_post_meta($parentId,$fieldKey, true);
    }
    if(!\is_string($fieldValue) || $fieldValue === ''){
        $fieldValue = $default;
    }
    return $fieldValue;
}

/**
 * Get the product image data (url, with, height)
 * @see: \WC_Product::get_image()
 *
 * @param \WC_Product $product
 * @param string $size
 * @param array $attr
 * @param bool $placeholder
 * @return array
 */
function getWCProductImageData( \WC_Product $product, $size = 'woocommerce_thumbnail', $attr = array(), $placeholder = true): array {
    $image = [];
    if($product->get_image_id()){
        $image = wp_get_attachment_image_src($product->get_image_id(), $size, false);
    }elseif($product->get_parent_id()){
        $parent_product = wc_get_product($product->get_parent_id());
        if ( $parent_product ) {
            $image = getWCProductImageData($parent_product, $size, $attr, $placeholder);
        }
    }

    if(!$image && $placeholder){
        $image = wc_placeholder_img_src($size);
    }

    if(!\is_array($image)){
        $image = [];
    }

    return $image;
}

/**
 * Get the data to render the mini cart. Useful to use in ajax calls.
 * @see: /woocommerce/templates/cart/mini-cart.php
 *
 * @return array
 */
function getMiniCartData(): array {
    if(!function_exists('WC')){
        return [];
    }
    if(!property_exists(WC(),'cart') || !WC()->cart instanceof \WC_Cart){
        return [];
    }
    if(WC()->cart->is_empty()){
        return [
            'no_items_message' => esc_html__('No products in the cart.', 'woocommerce')
        ];
    }
    $resultData = [
        'total_items_qty' => 0,
        'total_different_items_qty' => 0
    ];
    foreach ( WC()->cart->get_cart() as $cartItemKey => $cartItem ){
        $product = apply_filters( 'woocommerce_cart_item_product', $cartItem['data'], $cartItem, $cartItemKey );
        $productID = apply_filters( 'woocommerce_cart_item_product_id', $cartItem['product_id'], $cartItem, $cartItemKey );
        if ( $product && $product->exists() && $cartItem['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cartItem, $cartItemKey ) ) {
            $productName = apply_filters( 'woocommerce_cart_item_name', $product->get_name(), $cartItem, $cartItemKey );
            //$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $product->get_image(), $cartItem, $cartItemKey );
            $thumbnail = getWCProductImageData($product);
            $productPrice = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $product ), $cartItem, $cartItemKey );
            $productPrice = strip_tags($productPrice);
            //$productPriceXQuantity = apply_filters( 'woocommerce_cart_product_price', wc_price( $cartItem['line_total'] ), $product );
            //$productPermalink = apply_filters( 'woocommerce_cart_item_permalink', $product->is_visible() ? $product->get_permalink( $cartItem ) : '', $cartItem, $cartItemKey );
            $productPermalink = $product->is_visible() ? $product->get_permalink( $cartItem ) : '';
            $itemListClass = esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cartItem, $cartItemKey ) );
            /*$removeLink = apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                'woocommerce_cart_item_remove_link',
                sprintf(
                    '<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">&times;</a>',
                    esc_url( wc_get_cart_remove_url( $cartItemKey ) ),
                    esc_attr__( 'Remove this item', 'woocommerce' ),
                    esc_attr( $productID ),
                    esc_attr( $cartItemKey ),
					esc_attr( $_product->get_sku() )
                ),
                $cartItemKey
            );*/
            $removeLink = esc_url(wc_get_cart_remove_url($cartItemKey));
            $removeLinkLabel = esc_attr__('Remove this item', 'woocommerce');
            $sku = $product->get_sku();
            $itemData = [];
            /*
             * Getting item cart additional data
             * @see: wc_get_formatted_cart_item_data()
             */
            if ( $cartItem['data']->is_type( 'variation' ) && is_array( $cartItem['variation'] ) ) {
                foreach ( $cartItem['variation'] as $name => $value ) {
                    $taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );
                    if ( taxonomy_exists( $taxonomy ) ) {
                        $term = get_term_by( 'slug', $value, $taxonomy );
                        if ( ! is_wp_error( $term ) && $term && $term->name ) {
                            $value = $term->name;
                        }
                        $label = wc_attribute_label( $taxonomy );
                    }else {
                        // If this is a custom option slug, get the options name.
                        $value = apply_filters( 'woocommerce_variation_option_name', $value, null, $taxonomy, $cartItem['data'] );
                        $label = wc_attribute_label( str_replace( 'attribute_', '', $name ), $cartItem['data'] );
                    }
                    if ( '' === $value || wc_is_attribute_in_product_name( $value, $cartItem['data']->get_name() ) ) {
                        continue;
                    }
                    $itemData[] = [
                        'key' => $label,
                        'value' => $value,
                    ];
                }
            }
            $itemData = apply_filters( 'woocommerce_get_item_data', $itemData, $cartItem );
            foreach ( $itemData as $key => $data ) {
                // Set hidden to true to not display meta on cart.
                if (!empty( $data['hidden'])){
                    unset($itemData[$key]);
                    continue;
                }
                $itemData[$key]['key'] = ! empty($data['key']) ? $data['key'] : $data['name'];
                $itemData[$key]['display'] = ! empty($data['display']) ? $data['display'] : $data['value'];
            }
            /*
             * Finalize the item
             */
            $resultData['items'][] = [
                'key' => $cartItemKey,
                'product_name' => $productName,
                'sku' => $sku,
                'thumbnail' => $thumbnail,
                'price' => $productPrice,
                'permalink' => $productPermalink,
                'qty' => $cartItem['quantity'],
                'list_element_class' => $itemListClass,
                'remove_url' => $removeLink,
                'remove_url_label' => $removeLinkLabel,
                'data' => $itemData
            ];
            $resultData['total_items_qty'] += $cartItem['quantity'];
            $resultData['total_different_items_qty'] += 1;
        }
    }
    $subtotal = WC()->cart->get_cart_subtotal();
    $subtotal = strip_tags($subtotal);
    $resultData['subtotal'] = $subtotal;
    $resultData['view_cart_url'] = esc_url(wc_get_cart_url());
    $resultData['view_cart_label'] = esc_html__('View cart', 'woocommerce');
    $resultData['goto_checkout_url'] = esc_url(wc_get_checkout_url());
    $resultData['goto_checkout_label'] = esc_html__('Checkout', 'woocommerce');
    $resultData = apply_filters('waboot/woocommerce/cart_data', $resultData);
    return $resultData;
}