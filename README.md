# wp-plugins

plugins for my wordpress

## Configuration

Copier ethiry.config.sample.php en ethiry.config.php et adapter les valeurs.

**URL_TRAVELPHOTOS**: root URL de travelphotos, atteignable de n'importe où<br>
**DOCKER_TRAVELPHOTOS**: root URL de travelphotos:3000 de docker à docker<br>
**DEBUG**: true pour écrire des traces (boolean)<br>
**LOG_FILE**: path du fichier de traces<br>
**TOKEN**: authentification pour la route PUT /api/journal/photos/:postId. Il est généré par l'outil createToken.js dans travelphotos.<br>
**GOBACK_URL**: url de l'icône "retour de note"<br>

## photo2

Pour insérer des photos en provenance de travelphotos.

La photo est un lien vers la page de la photo sur travelphotos.

Quand le post est publié, la référence du post est envoyée à travelphotos (PUT /api/journal/photos/:postId), pour ajouter la mention "apparaît dans l'article xxx".

Exemple:

```
[etphoto2 filename="220918_172400H" title="Les%20ar%C3%A8nes%20d'Arles" voy_key="nimarles2022" show_caption="false"]
```

Pour obtenir le short code ci-dessus depuis travelphotos :

1. ouvrir la page d'un album
1. ajouter "?mode=wp" dans l'url
1. cliquer sur une photo
1. le short code est copié dans le presse-papier (et dans la console) sans autre manifestation

## footnote

Pour crée des notes de bas de pages.

Une note est créée en 2 parties, dans le corps du texte :

```
[note id=1]
```

et à la fin du post :

```
[noteText id=1]La note de base de page[/noteText]
```

L'id peut être n'importe quel caractère, en général un chiffre.

Attention: aucun test de cohérence n'est fait (correspondance entre les 2 parties, ordre des notes)

## additional.css

CSS à copier dans Apparence > Thème > Personnaliser > CSS additionel
