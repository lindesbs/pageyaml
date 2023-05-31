# pageYaml

Seitenbaeume via `yaml` Datei erzeugen.
Wahlweise mit erstem Artikel und erstem ContentElement.

```yaml
startseite:
  Uebersicht:
  Vorstellung:
  Links:
    Details:
  Impressum:
  Datenschutz:
  404:
  403:
  _HIDDEN:
    _Header:
    _Footer:
```

Aliase werden automatisch generiert. Beginnt das erste Wort mit einem "_" (Unterstrich) wird die Seite _versteckt_.