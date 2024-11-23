# pageYaml


Creating page trees via yaml file.
Optionally with the first article and the first content element.

```yaml
# Using the double tilde, the label can be separated from a fixed alias.
# Otherwise, the alias is formed from the label.
Homepage~~homepage:
  # By prefixing with a tilde, the individual attributes of a page can be addressed.
  ~description: The simple overview page
  ~language: en
  ~fallback: true
  ~includeLayout: true
  ~layout: 1
  Overview:
  Introdution:
  Links:
    Detail:
      ~visible: false
      ~hide: true
  Legal notice:
  Privacy Policy:
  # Numerical pages are parsed as error pages
  404:
  403:
  _HIDDEN:
    _Header:
    _Footer:
```

Aliases are generated automatically. If the first word starts with an underscore ("_"), the page is hidden.
