# Synthèse : risques résiduels

Ce qui est en place : namespace `web` en Pod Security `restricted`, RBAC limité, Network Policy deny-all, Deployment durci, Secret chiffré dans etcd, journal d'audit, scans Trivy et Checkov, pipeline de sécurité.

## Ce qui reste exposé

| Risque | Pourquoi il reste | Piste |
|--------|-------------------|-------|
| Vulnérabilités futures de l'image | 0 alerte HIGH ou CRITICAL aujourd'hui, mais de nouvelles CVE apparaissent chaque semaine | Relancer Trivy régulièrement et reconstruire l'image |
| Image du Deployment non corrigée | Le Deployment utilise l'image publique épinglée par digest, qui garde les 14 vulnérabilités corrigées seulement dans `tp1-hello`. Le digest ne reçoit pas non plus les correctifs de la base | Déployer l'image `tp1-hello` publiée dans un registre, et mettre à jour le digest automatiquement (Renovate ou Dependabot) |
| Clé de chiffrement des Secrets | La clé est un fichier sur le noeud du control-plane, quelqu'un qui accède au noeud peut la lire | KMS externe ou coffre de secrets |
| Secrets lisibles par l'API | Un utilisateur avec le droit `get secrets` lit la valeur en base64 | Droits RBAC minimaux, audit des lectures de Secrets |
| Journal d'audit au niveau Metadata | On voit qui a fait quoi, pas le contenu des requêtes | Niveau `Request` sur les ressources sensibles, envoi vers un SIEM |
| Network Policy testée sur une démo | Les règles sont validées avec deux pods de test, pas avec une vraie application | Tests réseau automatisés sur l'application réelle |
| Pas de contrôle d'admission avancé | Pod Security `restricted` ne vérifie ni la provenance ni la signature des images | Kyverno ou Gatekeeper, signature avec cosign |
| Approbation avant déploiement | Sur un dépôt privé en offre gratuite, les relecteurs obligatoires d'un environnement GitHub peuvent ne pas être disponibles | Vérifier l'offre, sinon déclenchement manuel du workflow |
| Database simulée | Le pod `database` est un faux serveur sur le port 5432, pas un vrai PostgreSQL | Vrai PostgreSQL avec ses volumes et ses identifiants en Secret |

## Conclusion

Les contrôles bloquent les déploiements non conformes les plus courants (pods root, privilégiés, réseau ouvert, secrets en clair, image vulnérable). Le plus gros risque restant est la maintenance dans le temps : les scans et les mises à jour de l'image doivent tourner en continu, pas une seule fois.
