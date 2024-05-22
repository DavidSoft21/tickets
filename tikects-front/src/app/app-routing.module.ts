import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SignUpComponent } from './components/auth/sign-up/sign-up.component';
import { LoginComponent } from './components/auth/login/login.component';

import { DashboardComponent } from './components/dashboard/dashboard.component';
import { UserComponent } from './components/pages/users/user/user.component';
import { PageNotFoundComponent } from './components/pages/page-not-found/page-not-found.component';
import { TicketComponent } from './components/pages/tickets/ticket/ticket.component';
// import { TicketStoreComponent } from './components/pages/tickets/ticket-store/ticket-store.component';
// import { TicketEditComponent } from './components/pages/tickets/ticket-edit/ticket-edit.component';
// import { TicketShowComponent } from './components/pages/tickets/ticket-show/ticket-show.component';
import { isUserAuthenticatedGuard } from './guards/auth.guard';
import { isGuestGuard } from './guards/auth.guard';

const routes: Routes = [
  { path: 'signup', component: SignUpComponent, canActivate: [isGuestGuard]},
  { path: 'login', component: LoginComponent, canActivate: [isGuestGuard]},
  { path: 'dashboard', component: DashboardComponent, canActivate: [isUserAuthenticatedGuard] },
  { path: 'user', component: UserComponent, canActivate: [isUserAuthenticatedGuard] },
  { path: 'tickets', component: TicketComponent, canActivate: [isUserAuthenticatedGuard] },
  // { path: 'ticket/:id', component: TicketComponent, canActivate: [isUserAuthenticatedGuard] },
  // { path: 'ticket-store', component: TicketStoreComponent, canActivate: [isUserAuthenticatedGuard] },
  // { path: 'ticket-edit/:id', component: TicketEditComponent, canActivate: [isUserAuthenticatedGuard] },
  // { path: 'ticket-show', component: TicketShowComponent },
  { path: '', redirectTo: '/login', pathMatch: 'full' },
  { path: '**', component: PageNotFoundComponent }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
