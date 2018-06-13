# File Upload (Angular2 + PHP + MySql)
This project was generated with [angular-cli](https://github.com/angular/angular-cli) version 1.0.0-beta.28.3.

I used "ng2-uploader" plugin for uploading files.
I used PHP for backend and mysql database.

To Run the code, you need to change the configurations as below :
1. With in file 'src\app\app.global.ts', change the 'API_ENDPOINT' where the php code resides. (ex: API_ENDPOINT = 'http://localhost/Php Backend')
2. For Database connection configuration check the file 'Php Backend\db.php'.
3. I am providing database file in 'Database File' folder.
4. For upload, in 'Php Backend' folder you must create 'uploads' folder and give write permission to the 'uploads' folder.



## Development server
Run `ng serve` for a dev server. Navigate to `http://localhost:4200/`. The app will automatically reload if you change any of the source files.



## Code scaffolding

Run `ng generate component component-name` to generate a new component. You can also use `ng generate directive/pipe/service/class/module`.



## Build

Run `ng build` to build the project. The build artifacts will be stored in the `dist/` directory. Use the `-prod` flag for a production build.



## Running unit tests

Run `ng test` to execute the unit tests via [Karma](https://karma-runner.github.io).



## Running end-to-end tests


Run `ng e2e` to execute the end-to-end tests via [Protractor](http://www.protractortest.org/).
Before running the tests make sure you are serving the app via `ng serve`.



## Deploying to GitHub Pages


Run `ng github-pages:deploy` to deploy to GitHub Pages.



## Further help


To get more help on the `angular-cli` use `ng help` or go check out the [Angular-CLI README](https://github.com/angular/angular-cli/blob/master/README.md).
