import { Component, Input, OnInit } from '@angular/core';
import { WebsocketService } from '../services/websocket.service';
import { FormBuilder } from '@angular/forms'

@Component({
  selector: 'app-chat',
  templateUrl: './chat.component.html',
  styleUrls: ['./chat.component.css']
})
export class ChatComponent implements OnInit {

  public listMessages: any = [];
  public emptyMessages: boolean = true;
  chatForm = this.formBuilder.group({
    message: ''
  })

  constructor(private websocket: WebsocketService, private formBuilder: FormBuilder,) { }

  ngOnInit(): void {
    this.websocket.getMessages().subscribe(data => {
      this.listMessages = data;
      this.emptyMessages = false;
    })
  }

  onSubmit(): void {
    this.listMessages = this.websocket.send(this.chatForm.value.message);
    this.emptyMessages = false;
    this.chatForm.reset();
  }

  
}
