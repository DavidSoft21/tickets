import { ComponentFixture, TestBed } from '@angular/core/testing';

import { TicketUserComponent } from './ticket-user.component';

describe('TicketUserComponent', () => {
  let component: TicketUserComponent;
  let fixture: ComponentFixture<TicketUserComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [TicketUserComponent]
    });
    fixture = TestBed.createComponent(TicketUserComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
