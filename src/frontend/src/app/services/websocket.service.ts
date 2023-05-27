import { Injectable } from '@angular/core';
import { Observable, Subject } from 'rxjs';
import {webSocket} from 'rxjs/webSocket';

export interface Message {
  name: string;
  content: string;
  me: boolean;
}

@Injectable({
  providedIn: 'root'
})
export class WebsocketService {
  
  public messages: any[] = [];
  websocket = webSocket('ws://localhost:9501');
  

  getMessages(): Observable<any> {
    var subject = new Subject<any>();
    this.websocket.subscribe((message) => {
      
      this.messages.push(message)

      subject.next(this.messages);
     
    })
    return subject.asObservable();
  }

  send(message: string) {
    let messageData: Message = {
        content: message,
        name: 'Lucas',
        me: false
    }
    this.websocket.next(messageData);

    messageData.me = true;
    this.messages.push(messageData)

    return this.messages;
  }
}
