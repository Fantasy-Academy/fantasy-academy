import React from 'react';

interface ButtonProps {
  type?: 'button' | 'submit' | 'reset';
  onClick?: () => void;
  text: string;
  className?: string;
  disabled?: boolean;  // <-- Přidáno
}

const Button: React.FC<ButtonProps> = ({ type = 'button', onClick, text, className = '' }) => {
  const textColorClass = /\btext-[a-zA-Z0-9-]+\b/.test(className) ? '' : 'text-white';

  return (
    <button
      type={type}
      onClick={onClick}
      className={`flex w-full justify-center hover:scale-[105%] px-6 py-3 text-base font-bold font-sourceSans3 transition-scale duration-[230ms] ${textColorClass} ${className}`}
    >
      {text}
    </button>
  );
};

export default Button;