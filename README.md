# Mietshop

## Technical Requirements

* MySQL DBMS
* Bootstrap 5.3
* HTTPS
* Mail server with SMTP support
* PHP 8.x webserver with file access and text editor

---

## Features

### CMS

Upload, edit, or delete:

* Rental objects (e.g., title, description, price options, photos, stock amount)
* Legal information (imprint, data privacy, business conditions)
* Social media links in the footer
* Business information (e.g., company name, CEO, contact)
* Database entries via your own interface (expert mode – handle with caution)

### Consent Banner

* Activate or disable services (e.g., Google Maps) upon first visit (to comply with EU regulations)
* Briefly informs the visitor which services and cookies are in use

---

## Planned Features

### CMS

* News feed on the landing page (**planned**)

### MVP Architecture

* Model-View-Presenter (MVP) architecture is planned to be implemented step-by-step.
* MVC is considered unsuitable because it does not fully separate the view from the model, which can lead to business or data logic leaking into the view.
* MVP is preferred for easier view replacement and better adherence to SOLID principles and clean architecture.
* Interfaces and abstract classes are being used to define contracts and reusable base functionality while keeping components decoupled.

---

## Architecture Overview

+-----------+&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+------------------+&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+-----------+ <br>
|&nbsp;&nbsp;&nbsp;Model&nbsp;&nbsp;&nbsp;|&nbsp;<--->&nbsp;|&nbsp;&nbsp;&nbsp;Presenter&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<---> |&nbsp;&nbsp;&nbsp;View&nbsp;&nbsp;&nbsp;&nbsp;| <br>
+-----------+&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+------------------+&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+-----------+

* The **Model** manages all data operations (`fetch`, `insert`, `delete`).
* The **Presenter** coordinates the interaction between Model and View, prepares data, and decides which View to render.
* The **View** is “dumb”: it only displays the data provided by the Presenter and forwards user actions back to the Presenter.
* A **Router** is used on the landing page to choose the suitable Presenter in the beginning (you need an entry point of course)

---

## Coding Principles

* Interfaces are used to define contracts between components.
* Abstract classes provide base functionality for reusability.
* Dependency Injection is applied where possible to decouple components.
* Lazy loading can be applied for Models and Views to optimize performance.
* The architecture follows SOLID principles to ensure maintainability, testability, and flexibility.  