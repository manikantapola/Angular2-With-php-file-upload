import { Component, OnInit } from '@angular/core';
import {Injectable } from '@angular/core';
import {Http, Headers, Response, Jsonp} from '@angular/http';
import {RequestOptions, Request, RequestMethod} from '@angular/http';
import {Observable} from 'rxjs/Rx';
import 'rxjs/Rx';
import 'rxjs/add/operator/map';
import { Router } from "@angular/router";
import { AppSettings } from '../app.global';
import { Renderer, ElementRef, ViewChild } from '@angular/core';

@Component({
  selector: 'app-list',
  templateUrl: './list.component.html',
  styleUrls: ['./list.component.css']
})
export class ListComponent implements OnInit {

	private list ;
  	uploadFile: any;

  @ViewChild('target') fileInput:ElementRef;

	options: Object = {
	  url: AppSettings.API_ENDPOINT+'upload.php',
	  filterExtensions: true,
	  allowedExtensions: ['application/json'],
	  fieldReset :true,
	  maxUploads:5,
    autoUpload: false
	};
    sizeLimit = 240000;
 
    constructor(private http:Http, private router:Router, private renderer:Renderer){
    }
  
    ngOnInit(){
    	console.log("check "+AppSettings.API_ENDPOINT);
    	this.getData();
    }

    getData(){
        let headers = new Headers();
        headers.append('Content-Type', 'application/json');

        return this.http.post(AppSettings.API_ENDPOINT+'action.php?request=getList', {
          headers: headers
        }).subscribe(
          result => { this.list = result.json().data;}
        );
    }

    upload(){

      var file_count = this.fileInput.nativeElement.files.length;
      if(file_count == 0){
        alert("Please select file");
      }
      this.renderer.invokeElementMethod(this.fileInput.nativeElement, 
        'dispatchEvent', 
        [new MouseEvent('dblclick')]);

        this.fileInput.nativeElement.value = '';

    }

    handleUpload(data): void {
	    if (data && data.response) {
	      data = JSON.parse(data.response);
	      this.uploadFile = '';
	      if(data.status == 'true'){
	        this.getData();
	      }
	    }
	}
 
  
  	beforeUpload(uploadingFile): void {
  		console.log(uploadingFile.message);
    	if(uploadingFile.message != ''){
      		uploadingFile.setAbort();
      		alert(uploadingFile.message);
      		return;
    	}

    	if (uploadingFile.size > this.sizeLimit) {
     		uploadingFile.setAbort();
      		alert('File size should be less than 240 KB');
    	}
  	}

    edit(document_id){
      this.router.navigate(["/info", document_id]);
    }

    delete(document_id){
      let headers = new Headers();
        headers.append('Content-Type', 'application/json');
        
        return this.http.post(AppSettings.API_ENDPOINT+'action.php?request=delete', {
          headers: headers,
          DocumentID: document_id,
         }).subscribe(
          result => { if(result.json().status == 'success'){ alert('Deleted Successfully');this.getData();} }
        );
    }
 

}
