import React, { forwardRef } from 'react';
import { cn } from '@/lib/utils';

interface FormFieldProps {
  label: string;
  error?: string;
  required?: boolean;
  description?: string;
  children: React.ReactNode;
  className?: string;
}

export function FormField({ 
  label, 
  error, 
  required = false, 
  description,
  children,
  className 
}: FormFieldProps) {
  const fieldId = React.useId();
  const errorId = `${fieldId}-error`;
  const descriptionId = `${fieldId}-description`;

  return (
    <div className={cn('space-y-2', className)}>
      <label 
        htmlFor={fieldId}
        className="block text-sm font-medium text-slate-700 dark:text-slate-300"
      >
        {label}
        {required && (
          <span className="text-red-500 ml-1" aria-label="required">
            *
          </span>
        )}
      </label>
      
      {description && (
        <p 
          id={descriptionId}
          className="text-sm text-slate-500 dark:text-slate-400"
        >
          {description}
        </p>
      )}
      
      <div className="relative">
        {React.cloneElement(children as React.ReactElement, {
          id: fieldId,
          'aria-invalid': error ? 'true' : 'false',
          'aria-describedby': cn(
            error && errorId,
            description && descriptionId
          ),
          'aria-required': required,
        })}
      </div>
      
      {error && (
        <p 
          id={errorId}
          className="text-sm text-red-600 dark:text-red-400"
          role="alert"
          aria-live="polite"
        >
          {error}
        </p>
      )}
    </div>
  );
}

interface InputProps extends React.InputHTMLAttributes<HTMLInputElement> {
  className?: string;
}

export const Input = forwardRef<HTMLInputElement, InputProps>(
  ({ className, type, ...props }, ref) => {
    return (
      <input
        type={type}
        className={cn(
          'flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm',
          'placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent',
          'disabled:cursor-not-allowed disabled:opacity-50',
          'dark:border-slate-600 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-500',
          className
        )}
        ref={ref}
        {...props}
      />
    );
  }
);
Input.displayName = 'Input';

interface TextareaProps extends React.TextareaHTMLAttributes<HTMLTextAreaElement> {
  className?: string;
}

export const Textarea = forwardRef<HTMLTextAreaElement, TextareaProps>(
  ({ className, ...props }, ref) => {
    return (
      <textarea
        className={cn(
          'flex min-h-[80px] w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm',
          'placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent',
          'disabled:cursor-not-allowed disabled:opacity-50 resize-vertical',
          'dark:border-slate-600 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-500',
          className
        )}
        ref={ref}
        {...props}
      />
    );
  }
);
Textarea.displayName = 'Textarea';

interface SelectProps extends React.SelectHTMLAttributes<HTMLSelectElement> {
  className?: string;
  options: Array<{ value: string; label: string; disabled?: boolean }>;
  placeholder?: string;
}

export const Select = forwardRef<HTMLSelectElement, SelectProps>(
  ({ className, options, placeholder, ...props }, ref) => {
    return (
      <select
        className={cn(
          'flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm',
          'focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent',
          'disabled:cursor-not-allowed disabled:opacity-50',
          'dark:border-slate-600 dark:bg-slate-800 dark:text-white',
          className
        )}
        ref={ref}
        {...props}
      >
        {placeholder && (
          <option value="" disabled>
            {placeholder}
          </option>
        )}
        {options.map((option) => (
          <option 
            key={option.value} 
            value={option.value}
            disabled={option.disabled}
          >
            {option.label}
          </option>
        ))}
      </select>
    );
  }
);
Select.displayName = 'Select';

interface CheckboxProps extends Omit<React.InputHTMLAttributes<HTMLInputElement>, 'type'> {
  label?: string;
  className?: string;
}

export const Checkbox = forwardRef<HTMLInputElement, CheckboxProps>(
  ({ label, className, ...props }, ref) => {
    const id = React.useId();

    return (
      <div className={cn('flex items-center space-x-2', className)}>
        <input
          type="checkbox"
          id={id}
          className={cn(
            'h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500',
            'dark:border-slate-600 dark:bg-slate-800',
            className
          )}
          ref={ref}
          {...props}
        />
        {label && (
          <label 
            htmlFor={id}
            className="text-sm font-medium text-slate-700 dark:text-slate-300"
          >
            {label}
          </label>
        )}
      </div>
    );
  }
);
Checkbox.displayName = 'Checkbox';

interface RadioGroupProps {
  name: string;
  options: Array<{ value: string; label: string; description?: string }>;
  value?: string;
  onChange?: (value: string) => void;
  className?: string;
}

export function RadioGroup({ 
  name, 
  options, 
  value, 
  onChange, 
  className 
}: RadioGroupProps) {
  return (
    <div className={cn('space-y-3', className)} role="radiogroup">
      {options.map((option) => (
        <div key={option.value} className="flex items-start space-x-3">
          <input
            type="radio"
            id={`${name}-${option.value}`}
            name={name}
            value={option.value}
            checked={value === option.value}
            onChange={() => onChange?.(option.value)}
            className="mt-1 h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800"
          />
          <div className="flex-1">
            <label 
              htmlFor={`${name}-${option.value}`}
              className="text-sm font-medium text-slate-700 dark:text-slate-300 cursor-pointer"
            >
              {option.label}
            </label>
            {option.description && (
              <p className="text-sm text-slate-500 dark:text-slate-400 mt-1">
                {option.description}
              </p>
            )}
          </div>
        </div>
      ))}
    </div>
  );
}

interface FormGroupProps {
  children: React.ReactNode;
  className?: string;
  title?: string;
}

export function FormGroup({ children, className, title }: FormGroupProps) {
  return (
    <fieldset className={cn('space-y-4', className)}>
      {title && (
        <legend className="text-lg font-semibold text-slate-900 dark:text-white">
          {title}
        </legend>
      )}
      {children}
    </fieldset>
  );
}

interface FormActionsProps {
  children: React.ReactNode;
  className?: string;
}

export function FormActions({ children, className }: FormActionsProps) {
  return (
    <div className={cn('flex items-center justify-end space-x-3 pt-4', className)}>
      {children}
    </div>
  );
}
