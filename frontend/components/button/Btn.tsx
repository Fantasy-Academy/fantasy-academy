import React from 'react';
import type { Button as ButtonType } from '../../types/button.types';

interface ButtonProps {
  button?: ButtonType; // Umožňuje, aby button byl volitelný
}

const Button: React.FC<ButtonProps> = ({ button = {} }) => { // Přidá výchozí hodnotu, aby button nebyl undefined
  const {
    type = 'button',
    onClick,
    text = 'Default',
    className = '',
    disabled = false,
  } = button; // Bezpečné destrukturování

  return (
    <button
      type={type}
      onClick={onClick}
      className={`flex w-full justify-center hover:scale-[105%] px-6 py-3 text-base font-bold font-sourceSans3 transition-scale duration-[230ms] ${className}`.trim()}
      disabled={disabled}
    >
      {text}
    </button>
  );
};

export default Button;