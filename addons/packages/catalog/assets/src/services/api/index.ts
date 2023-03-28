export interface Image {
  id: string;
  title: string;
  mediaType: string;
  width: number;
  height: number;
  link: string;
  baseUrl: string;
  attachedFile: string;
  sizes: {
    [size: string]:
      | {
          fileName: string;
          link: string;
          mediaType: string;
          width: number;
          height: number;
        }
      | undefined;
  };
}

type ProductType = 'simple' | 'variable' | 'grouped' | 'bundle' | 'external';

type StockStatus = 'instock' | 'outofstock';

export interface Product {
  id: string;
  slug: string;
  name: string;
  sku: string;
  description: string;
  excerpt: string;
  type: ProductType;
  price: number;
  basePrice: number;
  minPrice: number;
  minBasePrice: number;
  maxPrice: number;
  maxBasePrice: number;
  taxClass: string;
  stockStatus: StockStatus;
  averageRating: number;
  link: string;
  image?: Image;
  gallery: Image[];
  metadata: {
    [metaKey: string]: unknown;
  };
  taxonomies: {
    [taxonomy: string]: Term[] | undefined;
  };
  catalogData?: {
    minPrice: number;
    minBasePrice: number;
    maxPrice: number;
    maxBasePrice: number;
    variations?: {
      attribute: string;
      type: 'select' | 'color' | 'image';
      products: {
        id: number;
        sku: string;
        name: string;
        attributeTerm: string;
        price: number;
        basePrice: number;
        taxClass: string;
        stockStatus: StockStatus;
        data: any;
      }[];
    };
  };
}

export interface Term {
  id: string;
  name: string;
  slug: string;
  description: string;
  taxonomy: string;
  parent: string;
  children: Term[];
  badge?: Image;
}

export interface TaxFilter {
  op: 'or' | 'and' | 'not';
  terms: string[];
}

export interface ProductQuery {
  title?: string;
  searchLogic?: 'or' | 'and';
  minPrice?: number;
  maxPrice?: number;
  taxonomies?: {
    [taxonomy: string]: TaxFilter;
  };
  ids?: string[];
}

export interface CatalogQuery {
  query?: ProductQuery;
  limit?: number;
  offset?: number;
  postMetaIn?: string[];
  termMetaIn?: string[];
  order?: CatalogOrder;
  taxonomiesIn?: string[];
}

export enum CatalogOrder {
  Default = 'default',
  Alphabetic = 'alphabetic',
  MostSold = 'mostSold',
  BestRated = 'bestRated',
  MostRated = 'mostRated',
  PriceLowToHigh = 'priceLowToHigh',
  PriceHighToLow = 'priceHighToLow',
}

export interface TaxonomyQuery {
  parent?: string;
  productQuery?: ProductQuery;
  limit?: number;
  offset?: number;
  termMetaIn?: string[];
}

export class WcserviceClient {
  headers: { [header: string]: string } = {};

  constructor(public baseUrl: string) {}

  setLanguage(language: string): void {
    this.headers['Accept-Language'] = language;
  }

  getHeaders(headers: { [header: string]: string }): {
    [header: string]: string;
  } {
    return Object.assign(this.headers, headers);
  }

  async findProducts(query?: CatalogQuery): Promise<Product[]> {
    const res = await fetch(`${this.baseUrl}/products/find`, {
      method: 'POST',
      headers: this.getHeaders({
        'Content-Type': 'application/json',
      }),
      body: JSON.stringify(query ?? {}),
    });

    return await res.json();
  }

  async getProductCount(query?: ProductQuery): Promise<number> {
    const res = await fetch(`${this.baseUrl}/products/count`, {
      method: 'POST',
      headers: this.getHeaders({
        'Content-Type': 'application/json',
      }),
      body: JSON.stringify(query ?? {}),
    });

    return (await res.json()).count ?? 0;
  }

  async getPriceRange(
    query?: CatalogQuery,
  ): Promise<{ min: number; max: number }> {
    const res = await fetch(`${this.baseUrl}/products/priceRange`, {
      method: 'POST',
      headers: this.getHeaders({
        'Content-Type': 'application/json',
      }),
      body: JSON.stringify(query ?? {}),
    });

    return await res.json();
  }

  async findTaxonomyTerms(
    taxonomy: string,
    query?: TaxonomyQuery,
  ): Promise<Term[]> {
    const res = await fetch(
      `${this.baseUrl}/taxonomies/${taxonomy}/terms/find`,
      {
        method: 'POST',
        headers: this.getHeaders({
          'Content-Type': 'application/json',
        }),
        body: JSON.stringify(query ?? {}),
      },
    );

    return await res.json();
  }

  async findTaxonomyTermsHierarchically(
    taxonomy: string,
    query?: TaxonomyQuery,
  ): Promise<Term[]> {
    const res = await fetch(
      `${this.baseUrl}/taxonomies/${taxonomy}/terms/findHierarchical`,
      {
        method: 'POST',
        headers: this.getHeaders({
          'Content-Type': 'application/json',
        }),
        body: JSON.stringify(query ?? {}),
      },
    );

    return await res.json();
  }
}