import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TasksService } from '../services/tasks.service';
import { TaskFormComponent } from '../components/task-form/task-form.component';
import { TaskCardComponent } from '../components/task-card/task-card.component';
import { ToastService } from '../services/toast.service';
import { trigger, transition, style, animate } from '@angular/animations';

console.log('TasksComponent file loaded');

@Component({
  selector: 'app-tasks',
  standalone: true,
  imports: [CommonModule, TaskFormComponent, TaskCardComponent],
  templateUrl: './tasks.component.html',
  styleUrls: ['./tasks.component.css'],
  animations: [
    trigger('taskAnimation', [
      transition(':leave', [
        style({ opacity: 1, transform: 'translateX(0)' }),
        animate(
          '300ms ease-out',
          style({
            opacity: 0,
            transform: 'translateX(100px)',
          })
        ),
      ]),
      transition(':enter', [
        style({
          opacity: 0,
          transform: 'translateY(-100px)',
          height: 0,
        }),
        animate(
          '300ms ease-out',
          style({
            opacity: 1,
            transform: 'translateY(0)',
            height: '*',
          })
        ),
      ]),
    ]),
  ],
})
export class TasksComponent implements OnInit {
  tasks: any[] = [];
  error: string = '';

  constructor(private tasksService: TasksService, private toast: ToastService) {
    console.log('TasksComponent constructed');
  }

  ngOnInit(): void {
    this.loadTasks();
  }

  loadTasks() {
    this.tasksService.getTasks().subscribe({
      next: (data) => {
        console.log('Received tasks:', data);
        this.tasks = data;
      },
      error: (err) => {
        console.error('Error loading tasks:', err);
        this.toast.showError('Failed to load tasks');
      },
    });
  }

  addTask(task: { title: string; description: string }) {
    this.tasksService
      .addTask({ title: task.title, description: task.description })
      .subscribe({
        next: (response) => {
          this.tasks.unshift(response.data.data[0]);
          if(this.tasks.length > 5) {
            this.tasks.pop();
          }
          console.log('newtask', response);
          this.toast.showSuccess('Task added successfully');
          // this.loadTasks();
        },
        error: (error) => {
          this.toast.showError(error);
          console.error('Error creating task:', error);
        },
      });
  }

  deleteTask(id: number) {
    this.tasksService.deleteTask(id).subscribe({
      next: () => {
        const taskIndex = this.tasks.findIndex((task) => task.id === id);
        if (taskIndex > -1) {
          this.tasks.splice(taskIndex, 1);

          // Fetch next available task if we have less than 5 tasks
          if (this.tasks.length < 5) {
            // Get the minimum ID from current tasks
            const minId = Math.min(...this.tasks.map((t) => t.id));

            this.tasksService.getNextTask(minId).subscribe({
              next: (response) => {
                if (response && response.length > 0) {
                  setTimeout(() => {
                    this.tasks.push(response[0]);
                  }, 200);
                }
              },
            });
          }
        }
        this.toast.showSuccess('Task completed successfully');
      },
      error: (error) => {
        console.error('Error deleting task:', error);
        this.toast.showError('Failed to complete task');
      },
    });
  }
}
