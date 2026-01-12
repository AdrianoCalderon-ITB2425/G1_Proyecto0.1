# Mòdul 0379 – Projecte intermodular d'administració de sistemes informàtics en xarxa

## Activitat Pràctica P0.1 – Desplegament d’aplicació extagram

---

## Índex

- Consideracions prèvies  
- Objectiu  
- Sprints  
  - Sprint 1  
  - Sprint 2 i 3  
- Proves  
- Documentació i control de versions  
- Annexos  

---

## Consideracions prèvies

Cada membre de l'equip i grup serà responsable de la **totalitat** del contingut del projecte, independentment de si ha realitzat o no una part concreta. Tots han de conèixer totes les parts i defensar el seu contingut mitjançant preguntes que li pot fer el professorat. [file:4]

Cada membre de l'equip també és responsable de la custòdia de la totalitat del projecte, i tots els membres han de tenir sempre accés al projecte i treballar en equip. És un treball en **equip**, tots són responsables, i durant la realització del projecte també es valoren les capacitats clau. [file:4]

### Capacitats clau

Algunes de les capacitats clau fonamentals per al bon funcionament del grup i l'èxit del projecte són: [file:4]

1. **Comunicació**: Capacitat de compartir idees, donar i rebre feedback i mantenir a tothom informat per a una col·laboració efectiva.  
2. **Treball en equip**: Col·laborar amb altres, respectar opinions diverses i contribuir al bé comú del grup.  
3. **Resolució de problemes**: Identificar problemes, analitzar situacions i trobar solucions de manera conjunta.  
4. **Gestió del temps**: Organitzar-se i prioritzar tasques per complir terminis.  
5. **Lideratge**: Guiar i motivar els altres i assumir responsabilitats dins del grup.  
6. **Adaptabilitat**: Ajustar-se a canvis i noves situacions que poden sorgir durant el projecte.  
7. **Pensament crític**: Analitzar informació i idees de manera objectiva per prendre decisions informades.  
8. **Empatia**: Comprendre i respectar emocions i perspectives dels altres membres de l’equip.  

Desenvolupar aquestes capacitats millora l'eficiència del projecte i enriqueix l'experiència personal i professional de cada membre. [file:4]

### Important 1

Heu de continuar fent servir les eines apreses en projectes anteriors: [file:4]

- GitHub  
- Proofhub  
- Markdown  

Recordeu que en la rúbrica, tota la gestió, planificació i documentació tenen un pes substancial. [file:4]

### Important 2

Les reunions d’**sprint planning** i **sprint review** han de quedar documentades en una acta en Markdown, commitada al GitHub. Hi ha d’haver una captura de pantalla de Proofhub en el moment de l’acta. [file:4]

---

## Objectiu

Fer el desplegament d’una aplicació anomenada **extagram** que permet pujar imatges i publicar-les amb alta disponibilitat i escalabilitat. L’aplicació està desenvolupada en PHP i utilitza una base de dades per emmagatzemar la informació. [file:4]

Cal implementar una xarxa interconnectada a través del núvol que integri i comuniqui múltiples tecnologies i serveis amb seguretat, escollint l’entorn d’implementació. Es farà servir una web d’alta disponibilitat amb la següent estructura. [file:4]

### Arquitectura

Els elements del sistema són: [file:4]

- **S1 – nginx:alpine**:  
  - Servidor NGINX utilitzat com a proxy invers.  
  - Rep totes les peticions del navegador.  
  - Per a les peticions a `extagram.php` realitza balanceig de càrrega entre S2 i S3.  

- **S2 i S3 – php:fpm**:  
  - Servei PHP-FPM.  
  - S’encarreguen d'executar `extagram.php` (part dinàmica de la web).  

- **S4 – php:fpm**:  
  - Servei PHP-FPM que executa `upload.php`.  
  - Emmagatzema els fitxers (imatges) en un directori del servidor (part dinàmica 2).  

- **S5 – nginx:alpine**:  
  - Servidor NGINX que serveix les imatges carregades al directori del servidor a través de S4 (part estàtica).  

- **S6 – nginx:alpine**:  
  - Servidor NGINX que serveix `style.css` i `preview.svg` (part estàtica).  

- **S7 – mysql**:  
  - Conté la base de dades MySQL.  
  - Replica el contingut de la carpeta de fitxers; si aquesta no està disponible, els fitxers es carreguen com a blobs a la BBDD.  

En el projecte **P0.2** s’evolucionarà aquesta arquitectura per afegir seguretat i automatització en el desplegament. [file:4]

---

## Sprints

S’identifiquen els següents sprints, quinzenals de 10 hores de duració cadascun: [file:4]

- **S1:** 15/12/2025 - 19/01/2026  
- **S2:** 19/01/2026 - 27/01/2026  
- **S3:** 02/02/2026 - 10/02/2026  

### Sprint 1

- Fer un anàlisi del projecte i de les necessitats/tecnologies que s’empraran.  
- Documentar aquest anàlisi en un repositori de GitHub.  
- Primer disseny mínimament viable: servidor web funcional en NGINX o Apache en una sola màquina.  
- Repassar instal·lació i configuració de mòduls i serveis.  
- El sistema ha de carregar imatges per un dels dos mètodes: base de dades o upload a carpeta. [file:4]

### Sprint 2 i 3

- Segregar l’aplicació mitjançant Docker en local, simulant els servidors.  
- Tota la comunicació es fa en xarxa pont entre contenidors Docker, sense consideracions de seguretat ni caiguda de BBDD/servidor d’imatges.  
- Implementar proxy invers i balanceig cap als servidors S2–S3, i segregació de peticions cap a S4 (S5) i S6.  
- Amb Packet Tracer o eina similar, definir l’esquema de la xarxa. [file:4]

---

## Proves

Cal definir i executar proves sobre: [file:4]

- Operativa general de la web.  
- Caiguda de nodes redundants.  

---

## Documentació i control de versions

- Crear un repositori a **GitHub** per al projecte.  
- Configurar el servidor perquè es validi amb GitHub per intercanvi de clau pública/privada.  
- Documentar l’ús de control de versions i pujades a producció:  
  - `git add`  
  - `git commit -m`  
  - `git push`  
  - `git pull`  
  - `git clone`  

També cal crear un **arbre de documentació en Markdown** dins del repositori. [file:4]

---


