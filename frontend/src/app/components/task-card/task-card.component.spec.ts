import { ComponentFixture, TestBed } from '@angular/core/testing';
import { TaskCardComponent } from './task-card.component';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { By } from '@angular/platform-browser';

describe('TaskCardComponent', () => {
  let component: TaskCardComponent;
  let fixture: ComponentFixture<TaskCardComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [TaskCardComponent, MatCardModule, MatButtonModule],
    }).compileComponents();

    fixture = TestBed.createComponent(TaskCardComponent);
    component = fixture.componentInstance;
    component.task = {
      id: 1,
      title: 'Test Task',
      description: 'Test Description',
      completed: false,
    };
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  it('should display task title and description', () => {
    const title = fixture.debugElement.query(By.css('.task-card-task-title'));
    const description = fixture.debugElement.query(
      By.css('.task-card-task-description')
    );

    expect(title.nativeElement.textContent.trim()).toBe('Test Task');
    expect(description.nativeElement.textContent.trim()).toBe(
      'Test Description'
    );
  });

  it('should have correct styling classes', () => {
    const card = fixture.debugElement.query(By.css('.task-item'));
    const button = fixture.debugElement.query(
      By.css('button[mat-stroked-button]')
    );

    expect(card).toBeTruthy('Card should have task-item class');
    expect(button).toBeTruthy('Should have a stroked button');
  });

  it('should emit delete event when Done button is clicked', () => {
    spyOn(component.delete, 'emit');
    const button = fixture.debugElement.query(
      By.css('button[mat-stroked-button]')
    );

    button.nativeElement.click();

    expect(component.delete.emit).toHaveBeenCalledWith(1);
  });

  it('should handle task without description', () => {
    component.task = {
      id: 2,
      title: 'Task without description',
      completed: false,
    };
    fixture.detectChanges();

    const description = fixture.debugElement.query(
      By.css('.task-card-task-description')
    );
    expect(description.nativeElement.textContent.trim()).toBe('');
  });
});
