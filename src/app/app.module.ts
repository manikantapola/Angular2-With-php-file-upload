import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';

import { AppComponent } from './app.component';

import { RouterModule } from "@angular/router";
import { AppComponents, AppRoutes } from "./app.routing";
import { ListComponent } from './list/list.component';
import { Ng2UploaderModule } from 'ng2-uploader';
import { InfoComponent } from './info/info.component';


@NgModule({
  declarations: [
    AppComponent,
    ListComponent,
    InfoComponent
  ],
  imports: [
    BrowserModule,
    FormsModule,
    HttpModule,
    RouterModule,
    RouterModule.forRoot(AppRoutes),
    Ng2UploaderModule    
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
