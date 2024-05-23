import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { TicketService } from 'src/app/services/tickets/ticket.service';
import { FormGroup, FormBuilder } from '@angular/forms';
import { Ticket } from 'src/app/models/ticket.model';


@Component({
  selector: 'app-ticket-user',
  templateUrl: './ticket-user.component.html',
  styleUrls: ['./ticket-user.component.css']
})
export class TicketUserComponent {

  lendbooks: any;
  errors: any;

  constructor(
    private ticketService: TicketService,
    private router: Router,
    private fb: FormBuilder,

  ) {


  } 

  ngOnInit(): void {
    this.ticketService.showTicketUsers.subscribe(
      response => { this.lendbooks = response; }, 
      errors => this.handleErrors(errors),
    );
  }


  deleteLendBook(id: any, iControl: any): void {
    
    let userResponse = confirm("¿Desea eliminar el ticket?");
    if (userResponse) {
      this.cleanError();
      console.log(id);
      
      this.ticketService.delete(id).subscribe(
        response => this.handleResponse(response),
        errors => this.handleErrors(errors),
      );
    } else {
      this.router.navigateByUrl('/tickets');
    }

  }

  private handleResponse(response: any): void {
    alert(response.message);
    this.router.navigateByUrl('/dashboard');
  }

  private handleErrors(errors: any): void {
    alert('Unauthorizated or Was Ocurred An Error Internal');
    this.errors = errors.error.errors;
    this.router.navigateByUrl('/tickets');
  }

  private cleanError(): void {
    this.errors = null;
  }
}
