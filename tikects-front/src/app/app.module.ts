import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { AppRoutingModule } from './app-routing.module';
import { ReactiveFormsModule } from '@angular/forms';
import { AppComponent } from './app.component';
import { SignUpComponent } from './components/auth/sign-up/sign-up.component';
import { LoginComponent } from './components/auth/login/login.component';
import { DashboardComponent } from './components/dashboard/dashboard.component';
import { AuthInterceptor } from './interceptors/auth.interceptor';
import { PageNotFoundComponent } from './components/pages/page-not-found/page-not-found.component';
import { UserComponent } from './components/pages/users/user/user.component';
import { MenuComponent } from './components/menu/menu.component';
import { HeaderComponent } from './components/shared/header/header.component';
import { FooterComponent } from './components/shared/footer/footer.component';
import { UserListComponent } from './components/pages/users/user-list/user-list.component';
import { UserStoreComponent } from './components/pages/users/user-store/user-store.component';
import { UserEditComponent } from './components/pages/users/user-edit/user-edit.component';
import { TicketComponent } from './components/pages/tickets/ticket/ticket.component';
// import { TicketEditComponent } from './components/pages/tickets/tickets-edit/ticket-edit.component';
// import { TicketStoreComponent } from './components/pages/tickets/tickets-store/ticket-store.component';
// import { TicketShowComponent } from './components/pages/tickets/tickets-show/ticket-show.component';

@NgModule({
  declarations: [
    AppComponent,
    SignUpComponent,
    LoginComponent,
    DashboardComponent,
    PageNotFoundComponent,
    UserComponent,
    MenuComponent,
    HeaderComponent,
    FooterComponent,
    UserListComponent,
    UserStoreComponent,
    UserEditComponent,
    TicketComponent,
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    ReactiveFormsModule,
  ],
  providers: [
    { provide: HTTP_INTERCEPTORS, useClass: AuthInterceptor, multi: true}
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
