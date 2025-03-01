import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { catchError, tap } from 'rxjs/operators';
import { ToastService } from './toast.service';

@Injectable({
  providedIn: 'root',
})
export class TasksService {
  private apiUrl = 'http://localhost:8000/api';

  constructor(private http: HttpClient, private toast: ToastService) {}

  getTasks(): Observable<any[]> {
    const url = `${this.apiUrl}/read.php`;
    console.log('Calling API:', url);
    return this.http.get<any[]>(url).pipe(
      tap((tasks) => console.log('Received tasks:', tasks)),
      catchError((error) => {
        console.error('Error fetching tasks:', error);
        this.toast.showError('Failed to load tasks');
        throw error;
      })
    );
  }

  addTask(task: { title: string; description: string }): Observable<any> {
    return this.http.post(`${this.apiUrl}/create.php`, task).pipe(
      tap(() => this.toast.showSuccess('Task created successfully')),
      catchError((error) => {
        console.error('Error creating task:', error);
        this.toast.showError('Failed to create task');
        throw error;
      })
    );
  }

  deleteTask(id: number): Observable<any> {
    return this.http.post(`${this.apiUrl}/delete.php`, { id }).pipe(
      tap(() => this.toast.showSuccess('Task completed successfully')),
      catchError((error) => {
        console.error('Error completing task:', error);
        this.toast.showError('Failed to complete task');
        throw error;
      })
    );
  }

  getNextTask(currentMinId: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/read.php?id=${currentMinId}`);
  }
}
