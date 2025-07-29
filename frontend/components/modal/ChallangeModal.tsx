'use client';
import React, { useState } from 'react';

interface ChallengeModalProps {
  title: string;
  description: string;
  duration: number;
  isCompleted: boolean;
  onClose: () => void;
}

const ChallengeModal: React.FC<ChallengeModalProps> = ({ title, description, onClose }) => {
  const [btnStates, setBtnStates] = useState<Record<string, boolean>>({});

  const handleButtonClick = (btnText: string) => {
    setBtnStates(prev => ({
      ...prev,
      [btnText]: !prev[btnText]
    }));
  };

  const isAnySelected = Object.values(btnStates).includes(true);

  return (
    <div className="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 p-4">
      <div className="bg-darkWhite p-8 rounded-lg shadow-lg w-[95%] max-w-2xl max-h-[90vh] overflow-y-auto relative">
        <button
          onClick={onClose}
          className="absolute top-4 right-4 text-charcoal text-2xl hover:scale-110 transition-transform duration-[140ms]"
        >
          ✖
        </button>

        <div className="flex flex-col pb-4">
          <div>
            <h2 className="text-5xl font-bold text-vibrantCoral text-center">{title}</h2>
            <h3 className="font-bebasNeue text-4xl text-charcoal mt-10">Coach&apos;s Corner</h3>
            <p className="text-lg text-charcoal">{description}</p>
          </div>
          <hr className="border-0 h-[2px] bg-charcoal my-4" />
        </div>

        <div className="max-h-[185px] overflow-y-auto">
          <div className="grid grid-cols-2 gap-4">
            {["A", "B", "C", "Martinez & Chuanez dorez toro", "E", "F", "G", "H", "I", "J"].map((text) => {
              const isSelected = btnStates[text];
              return (
                <button
                  key={text}
                  onClick={() => handleButtonClick(text)}
                  type="button"
                  className={`
                    w-full text-center py-3 px-4 rounded
                    transition-all duration-200
                    font-bold text-base font-sourceSans3
                    ${isSelected ? 'bg-goldenYellow text-black' : 'bg-white text-charcoal'}
                    hover:scale-[105%]
                  `}
                >
                  {text}
                </button>
              );
            })}
          </div>
        </div>

        <div className="mt-6">
          <button
            type="button"
            onClick={isAnySelected ? () => alert("Bet placed!") : undefined}
            disabled={!isAnySelected}
            className={`
              w-full py-4 text-lg font-bold text-white text-center
              transition-all duration-200
              font-sourceSans3
              ${isAnySelected ? 'bg-vibrantCoral cursor-pointer' : 'bg-coolGray cursor-not-allowed'}
            `}
          >
            Pick a bet!
          </button>
        </div>
      </div>
    </div>
  );
};

export default ChallengeModal;