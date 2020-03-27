ResomediaUsefulBundle 3 - autocomplete easy
===============================================

Fork from ShtumiUsefulBundle

## Installation

### Add the following lines to your  `deps` file and then run `php bin/vendors install`:

```
"resomedia/useful-bundle": "4.*"

```

### Add ResomediaUsefulBundle to your application kernel
```
    // config/bundles.php
    return [
        // ...
        Resomedia\UsefulBundle\ResomediaUsefulBundle::class => ['all' => true],
        // ...
    ];
```

### Import routes

```
resomedia_useful:
    resource: "@ResomediaUsefulBundle/Controller/"
    type: annotation
```

### Add form theming to twig (you can override this view)
```
twig:
    ...
    form_theme:
            - 'ResomediaUsefulBundle:Form:fields_useful.html.twig'
```

### Load jQuery to your views and this
```
    <script type="text/javascript" src="{{ asset('bundles/resomediauseful/js/bootstrap3-typeahead.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('bundles/resomediauseful/js/useful.js') }}"></script>
```

###Configuration

// app/config/config.yml
```
resomedia_useful:
    autocomplete_entities:
        users:
            class: AcmeDemoBundle:User
            choice_label: fullname
            role: ROLE_ADMIN
            property: email
            where: 'activate = 1'
            encrypted: true

        products:
            class: AcmeDemoBundle:Product
            choice_label: name
            role: ROLE_ADMIN
            search: contains|ends_with|begins_with
            case_insensitive: true
```
- **class** - Doctrine model.
- **role** - User role to use form type. Default: *IS_AUTHENTICATED_ANONYMOUSLY*. It needs for security reason.
- **choice_label** - Property that will be prompted by autocomplete. Default: *title*.
- **search** - LIKE format to get autocomplete values. You can use:
   - *begins_with* - LIKE 'value%' (**default**)
   - *ends_with* - LIKE '%value'
   - *contains*  - LIKE '%value%'
- **where** - your condition
- **case_insensitive** - Whether or not matching should be case sensitive or not
- **encrypted** - true / false, if your field is encrypt. (**Soon**)

###Usage

    $formBuilder->add('user', AjaxAutocompleteType::class, array(
        'entity_alias' => 'users',
        'label' => 'search user',
        'required' => false,
        'attr' => array('class' => 'form-control')
    ));

###V3.0
It is a lite version with only ajax autocomplete.
But the documentation is up to date and add symfony3 compatibility.
For more tools in UsefulBundle check ShtumiUsefulBundle.
###V4.0
Add symfony4 compatibility.