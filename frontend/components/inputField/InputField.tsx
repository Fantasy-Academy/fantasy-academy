import React from 'react';

interface InputFieldProps {
  id: string;
  label: string;
  value: string;
  name: string;
  onChange: React.ChangeEventHandler<HTMLInputElement>;
  type: string;
  autoComplete: string;
  required: boolean;
  error?: string;
  placeholder: string;
}

const InputField: React.FC<InputFieldProps> = ({
  id,
  label,
  value,
  name,
  type,
  onChange,
  autoComplete,
  required,
  error,
  placeholder,
}) => {
  return (
    <div className='mt-4'>
      <label htmlFor={id} className="block text-lg font-sourceSans3 font-semibold text-charcoal">
        {label}
      </label>
      <div>
        <input
          id={id}
          value={value}
          name={name}
          type={type}
          onChange={onChange}
          required={required}
          autoComplete={autoComplete}
          placeholder={placeholder}
          className={`block w-full rounded font-nunito text-base border ${
            error ? 'border-red-500' : 'border-transparent'
          } py-4 px-2 text-charcoal placeholder:text-coolGray focus:outline-none focus:ring-1 ${
            error ? 'focus:ring-red-500' : 'focus:ring-charcoal'
          } sm:text-sm`}
        />
        {error && <p className="mt-1 text-sm text-red-600">{error}</p>}
      </div>
    </div>
  );
};

export default InputField;