import { Component } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { FormGroup, FormBuilder } from '@angular/forms';
import { TicketService } from 'src/app/services/tickets/ticket.service';
import { Ticket } from '../../../../models/ticket.model';

@Component({
  selector: 'app-ticket-show',
  templateUrl: './ticket-show.component.html',
  styleUrls: ['./ticket-show.component.css']
})
export class TicketShowComponent {

  id:any;
  showForm: FormGroup;
  ticket: any = {};
  data: any;
  errors: any;
  

  constructor(
    private ticketService: TicketService,
    private router: Router,
    private activatedRoute: ActivatedRoute,
    private fb: FormBuilder,
  
  ) {
    this.id = activatedRoute.snapshot.paramMap.get('id');
    this.showForm = this.fb.group({
      id : [''],
    });
  }

  ngOnInit(): void {
    
  }

  private handleResponse(response: any): void {
    this.data = response.response;
  }

  private handleErrors(errors: any): void {
    this.data = null;
    this.errors = errors.error.errors;
  }

  private cleanError(): void {
    this.errors = null;
  }

  showBook() {
    this.cleanError();
    const id = this.showForm.value.id;
    this.ticketService.show(id).subscribe(
        response => this.handleResponse(response),
        error => this.handleErrors(error)
    );
  }
}