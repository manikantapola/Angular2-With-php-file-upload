import { Component, OnInit } from '@angular/core';
import { Router } from "@angular/router";
import { ActivatedRoute } from '@angular/router';

import {Injectable } from '@angular/core';
import {Http, Headers, Response, Jsonp} from '@angular/http';
import {RequestOptions, Request, RequestMethod} from '@angular/http';
import {Observable} from 'rxjs/Rx';
import 'rxjs/Rx';
import 'rxjs/add/operator/map';
import { AppSettings } from '../app.global';

@Component({
  selector: 'app-info',
  templateUrl: './info.component.html',
  styleUrls: ['./info.component.css']
})
export class InfoComponent implements OnInit {

  private doc_id;
  private info_view;

  constructor(private http:Http, private router:Router, private route: ActivatedRoute){
  }

  ngOnInit() {
  	
  	this.route.params.subscribe(params => {
      this.doc_id = params['id']; // --> Name must match wanted parameter
    });
    console.log("DOC ID "+this.doc_id);
    this.getInfo(this.doc_id);
  }

  backToList(){
      this.router.navigate(["/list"]);
  }

  getInfo(document_id){
      let headers = new Headers();
        headers.append('Content-Type', 'application/json');
        
        return this.http.post(AppSettings.API_ENDPOINT+'index.php?', {'request':'info', 'DocumentID': document_id}, {
          headers: headers
         }).subscribe(
          result => { if(result.json().status == 'success'){this.info_view = result.json().info;console.log(result.json().info)} }
        );
    }

}
