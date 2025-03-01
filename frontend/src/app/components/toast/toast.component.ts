import { Component, inject } from '@angular/core';
import {
  MAT_SNACK_BAR_DATA,
  MatSnackBarModule,
} from '@angular/material/snack-bar';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-toast',
  standalone: true,
  imports: [CommonModule, MatSnackBarModule],
  template: `
    <div class="toast-container" [class]="data.type">
      <span class="message">{{ data.message }}</span>
    </div>
  `,
  styles: [
    `
      .toast-container {
        padding: 14px 16px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        gap: 8px;
      }

      .success {
        background: #4caf50;
        color: white;
      }

      .error {
        background: #f44336;
        color: white;
      }

      .message {
        font-size: 14px;
      }
    `,
  ],
})
export class ToastComponent {
  data: { message: string; type: 'success' | 'error' } =
    inject(MAT_SNACK_BAR_DATA);
}
