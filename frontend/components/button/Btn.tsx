import React from 'react';

interface ButtonProps {
  children: React.ReactNode;
  type?: "button" | "submit";
  onClick?: () => void;
}

const Btn: React.FC<ButtonProps> = ({ children, type = "button", onClick }) => {
  return (
    <button
      type={type}
      onClick={onClick}
      className='flex w-full justify-center hover:scale-[105%] px-6 py-3 text-base font-bold font-sourceSans3 transition-scale duration-[230ms] bg-goldenYellow'
    >
      {children}
    </button>
  );
};

export default Btn;