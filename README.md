# Waboot Theme

Waboot aims to be a general purpose theme focused on development speed and flexibility; plus, it's component-based nature allows developers to use only the features they need and helps them to keep the source organized and easy to maintain.

A sample theme child is provided to get you started immediately.

## Usage

- Download the precompiled version [here](http://update.waboot.org/resource/get/theme/waboot) or download this repository.
- Copy `waboot-child` directory into `wb-content/` and rename it accordingly to your project.
- Follow the readme inside the child theme.
 
## Key concepts

Waboot is based on the concept of **zones** and is built through **components**. 

Components are self-contained micro-plugins that implement specific feature (like: breadcrumbs, lazyloading, different types of headers and footers...).

Zones are layout partials that, by default, are rendered blank (or not rendered at all) unless some component is attached to them. 

Components hook to zones with a specific weight. Components with lowest weight are rendered before components with higher weight.
 
You can move around components by edit their render zone and weight through WordPress dashboard.

Learn more about [zones](#zones) and [components](#components).

## No more template overriding

Waboot feature a revisited template hierarchy system which goal is to keep template overriding at minimum.

Learn more about [here](#template).

# Zones
<a href="#zones"></a>

Waboot provide flexibility through **zones**. Zones are layout partials to which components can attach. 

## Zones and components

Components can be easily moved between zones through WordPress dashboard:

!["Selecting a zone"](docs/assets/images/zones_01.png)

Here you can change the component position (zone) and priority (weight). Like WordPress hook system, lower priority components are rendered before higher ones.

## Predefined zones

Waboot feature some predefined zones that can be visualized here:

## Advanced topics

Under the hood zones are just WordPress actions with standardized names. `\Waboot\Layout` provides a quick API to them.

### Create a new zone

Create a new zone is easy:

- Register the zone  

    ```
    Waboot()->layout->create_zone(string <zone_name>, [\WBF\components\mvc\View|string|FALSE <zone_view>, array <zone_params>]);
    ```
    
    The only required param is the zone name. Zone name can be any `[a-z-]` string. 
    
    The view (when provided) can be a WBF View or a string. If a string is provided, the template will be retrieved via locate_template.

- Render the zone

    To render a zone, you only need to call `Waboot()->layout->render_zone(<zone_name>);` where you want to render it.
    
    If a template is assigned to the zone, make sure you included `Waboot()->layout->do_zone_action(<zone_name>);` within the that template.

### Hook to a zone programmatically

When a zone is registered, Waboot prepares an action with a name like: "`waboot/zones/<zone_name>`". You can hook directly to this hook or use Layout API:

```php
\Waboot()->layout->add_zone_action(<zone_name>,<call_back>);
```

# Components
<a href="#components"></a>

# Template system
<a href="#template"></a>
