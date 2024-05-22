import { Component } from '@angular/core';
import { FormGroup, FormBuilder } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { Ticket } from 'src/app/models/ticket.model';
import { TicketService } from 'src/app/services/tickets/ticket.service';

@Component({
  selector: 'app-ticket-store',
  templateUrl: './ticket-store.component.html',
  styleUrls: ['./ticket-store.component.css']
})
export class TicketStoreComponent {

  id:any;
  createForm: FormGroup;
  ticket: any = {};
  data: any;
  errors: any;
  

  constructor(
    private ticketService: TicketService,
    private router: Router,
    private activatedRoute: ActivatedRoute,
    private fb: FormBuilder
  ) {
  
    this.createForm = this.fb.group({
      title : [''],
      description: [''],
      deadline: [''],
      user_id : [''],
      status_id :[''],
    });
  }

  ngOnInit(): void {
    
  }

  private handleResponse(response: any): void {
    response.message ? 'undefined' : response =  alert(`
    id:  ${response.response.id},
    title: ${response.response.title},
    deadline:  ${response.response.deadline}`);
    this.router.navigateByUrl('/tickets');
  }

  private handleErrors(errors: any): void {
    errors.error.message ? 'undefined' : errors.error.message = 'An error has occurred!';
    alert(errors.error.message)
    this.errors = errors.error.errors;
  }

  private cleanError(): void {
    this.errors = null;
  }

  createTicket() {
    this.cleanError();
    this.ticketService.store(this.createForm.value).subscribe(
      response => this.handleResponse(response),
      error => this.handleErrors(error)
    );
  }
}
