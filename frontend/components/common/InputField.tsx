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
}

const InputField: React.FC<InputFieldProps> = ({ id, label, value, name, type, onChange, autoComplete, required }) => {
  return (
    <div>
      <label htmlFor={id} className="block text-sm font-medium text-gray-900">
        {label}
      </label>
      <div className="mt-2">
        <input
          id={id}
         value={value}
          name={name}
          type={type}
          onChange={onChange}
          required={required}
          autoComplete={autoComplete}
          className="block w-full rounded-md border-0 py-1.5 pl-2 pr-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-gray-800 sm:text-sm"
        />
      </div>
    </div>
  );
};

export default InputField;