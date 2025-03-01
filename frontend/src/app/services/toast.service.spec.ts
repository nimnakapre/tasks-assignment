import { TestBed } from '@angular/core/testing';
import { ToastService } from './toast.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { ToastComponent } from '../components/toast/toast.component';

describe('ToastService', () => {
  let service: ToastService;
  let snackBar: jasmine.SpyObj<MatSnackBar>;

  beforeEach(() => {
    const spy = jasmine.createSpyObj('MatSnackBar', ['openFromComponent']);
    TestBed.configureTestingModule({
      providers: [
        ToastService,
        { provide: MatSnackBar, useValue: spy }
      ]
    });
    service = TestBed.inject(ToastService);
    snackBar = TestBed.inject(MatSnackBar) as jasmine.SpyObj<MatSnackBar>;
  });

  it('should show success toast', () => {
    service.showSuccess('Test message');
    expect(snackBar.openFromComponent).toHaveBeenCalledWith(
      ToastComponent,
      {
        data: { message: 'Test message', type: 'success' },
        duration: 3000,
        horizontalPosition: 'right',
        verticalPosition: 'top'
      }
    );
  });

  it('should show error toast', () => {
    service.showError('Test error');
    expect(snackBar.openFromComponent).toHaveBeenCalledWith(
      ToastComponent,
      {
        data: { message: 'Test error', type: 'error' },
        duration: 3000,
        horizontalPosition: 'right',
        verticalPosition: 'top'
      }
    );
  });
}); 