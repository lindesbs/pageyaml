# pageYaml

Seitenbaeume via `yaml` Datei erzeugen.
Wahlweise mit erstem Artikel und erstem ContentElement.

```yaml
# mit der doppelten Tilde kann die Bezeichnung von einem fixen Alias getrennt werden.
# ansonsten wird der Alias ueber die Bezeichnung gebildet.
Die Startseite~~startseite:
  # Mit einer Tilde vorweg koennen die einzelnen Attribute einer Seite angesprochen werden
  ~description: Die einfache Uebersichtseite
  ~language: de
  ~fallback: true
  ~includeLayout: true
  ~layout: Standard
  Uebersicht:
  Vorstellung:
  Links:
    Details:
      ~visible: false
      ~hide: true
  Impressum:
  Datenschutz:
  # numerische Seiten werden als Fehlerseiten geparst
  404:
  403:
  _HIDDEN:
    _Header:
    _Footer:



```

Aliase werden automatisch generiert. Beginnt das erste Wort mit einem "_" (Unterstrich) wird die Seite _versteckt_.