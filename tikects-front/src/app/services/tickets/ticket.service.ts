import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { Ticket } from 'src/app/models/ticket.model';
import { environment } from 'src/environments/environment.development';

@Injectable({
  providedIn: 'root'
})
  
export class TicketService {
  
  private readonly API_URL =  environment.api_url;

  constructor(private http: HttpClient) { }

  store(ticket: Ticket): Observable<any> {
    let date = new Date(ticket.deadline);
    let day = date.getDate();
    let month = date.getMonth() + 1; 
    let year = date.getFullYear();

    if (day < 10) day = Number('0' + day.toString());
    if (month < 10) month = Number('0' + month.toString());

    ticket.deadline = `${day}-${month}-${year}`;
    return this.http.post(`${this.API_URL}/tickets/store`, ticket);
  };

  index() {
    return this.http.get(`${this.API_URL}/tickets/index`);
  }

  showTicketUsers(id: any) {
    return this.http.get(`${this.API_URL}/tickets/index/`+id);
  }

  delete(id:any) {
    return this.http.delete(`${this.API_URL}/tickets/destroy/`+id);
  }

  update(id: any,ticket:any) {
    return this.http.put(`${this.API_URL}/tickets/update/${id}`, ticket);
  }

  show(id: any): Observable<any> {
    return this.http.get(`${this.API_URL}/tickets/show/${id}`);
  };

}
