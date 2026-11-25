# Répartition des tâches

| Tâche | Intitulé | Responsable | Statut |
|-------|----------|-------------|--------|
| 1 | Dockerfile | Tom et Thomas | À faire |
| 2 | Optimiser et sécuriser l'image | Thomas | À faire |
| 3 | Déployer sur Kubernetes | Aymeric | À faire |
| 4 | Ressources et scalabilité | Antoine | À faire |
| 5 | Analyse VM contre conteneur | Julie | À faire |
| 6 | Rendu | Tom | À faire |

Statuts : À faire, En cours, Fait. Mettre à jour la colonne dès qu'une tâche change d'état.

## Tâche 1 : Dockerfile (Tom et Thomas)

- [ ] Écrire `docker/Dockerfile` : base nginx, copie de `app/`, port 8080
- [ ] Adapter `compose.yml` en `build:` au lieu de `nginx:latest` avec bind mount
- [ ] Tester avec `docker build` puis `docker run`

## Tâche 2 : Optimiser et sécuriser l'image (Thomas)

- [ ] Base légère (alpine) et utilisateur non-root (`nginxinc/nginx-unprivileged`)
- [ ] `.dockerignore`, pas de secrets dans l'image
- [ ] Mesurer la taille avant et après (par exemple `nginx:latest` contre l'image alpine), chiffre à mettre dans le rendu
- [ ] Vérifier le non-root avec `docker run --rm <image> id`

## Tâche 3 : Déployer sur Kubernetes (Aymeric)

- [ ] Installer `kind` et `kubectl`
- [ ] Créer le cluster et charger l'image (`kind load docker-image`)
- [ ] `kubectl apply -f k8s/` et vérifier que les pods sont `Running`
- [ ] Les manifests `deployment.yaml` et `service.yaml` existent déjà mais visent l'image `tp1-hello:1.0` : la tâche 1 doit produire ce nom, ou le manifest est adapté

## Tâche 4 : Ressources et scalabilité (Antoine)

- [ ] Les `requests` et `limits` CPU et mémoire et les 3 replicas sont déjà dans le manifest
- [ ] `kubectl scale` pour changer le nombre de replicas
- [ ] `kubectl describe pod` pour montrer les limites
- [ ] Captures pour le rendu

## Tâche 5 : Analyse VM contre conteneur (Julie)

- [ ] Rédiger l'analyse : isolation, performance, sécurité (noyau partagé, surface d'attaque, démarrage, poids)
- [ ] Placer le texte dans le README ou dans `doc/`

## Tâche 6 : Rendu (Tom)

- [ ] Captures et résultats des tâches 2 à 4 dans le README
- [ ] CI verte
- [ ] Dernière vérification que tout se lance sur Mac et Windows
