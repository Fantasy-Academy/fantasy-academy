import React from 'react';

interface ButtonProps {
  type?: 'button' | 'submit' | 'reset';
  onClick?: () => void;
  text: string;
  className?: string;
}

const Button: React.FC<ButtonProps> = ({ type = 'button', onClick, text, className }) => {
  return (
    <button
      type={type}
      onClick={onClick}
      className={`flex w-full justify-center px-6 py-3 text-base font-bold text-white font-sourceSans3 shadow-none hover:shadow-sharp transition-shadow duration-[230ms] ${className}`}
    >
      {text}
    </button>
  );
};

export default Button;