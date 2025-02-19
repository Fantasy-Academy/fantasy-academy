'use client';
import React, { useState } from 'react';
import Btn from "../button/Btn";

interface ChallengeModalProps {
    title: string;
    description: string;
    duration: number;
    isCompleted: boolean;
    onClose: () => void;
}

const ChallengeModal: React.FC<ChallengeModalProps> = ({ title, description, onClose }) => {
    const [btnColors, setBtnColors] = useState<Record<string, string>>({});
    
    const handleButtonClick = (btnText: string) => {
        setBtnColors(prev => {
            const newColors = { ...prev, [btnText]: prev[btnText] === 'bg-goldenYellow' ? 'bg-white' : 'bg-goldenYellow' };
            return newColors;
        });
    };

    const isAnySelected = Object.values(btnColors).includes('bg-goldenYellow');

    return (
        <div className="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 p-4">
            <div className="bg-darkWhite p-8 rounded-lg shadow-lg w-[95%] max-w-2xl max-h-[90vh] overflow-y-auto relative">
                <button
                    onClick={onClose}
                    className="absolute top-4 right-4 text-charcoal text-2xl hover:scale-110 transition-transform duration-[140ms]">
                    ✖
                </button>
                <div className='flex flex-col pb-4'>
                    <div>
                        <h2 className="text-5xl font-bold text-vibrantCoral text-center">{title}</h2>
                        <h3 className='font-bebasNeue text-4xl text-charcoal mt-10'>Coach's Corner</h3>
                        <p className="text-lg text-charcoal">{description}</p>
                    </div>
                    <hr className="border-0 h-[2px] bg-charcoal my-4" />
                </div>
                <div className="max-h-[185px] overflow-y-auto">
                    <div className='grid grid-cols-2 gap-4'>
                        {["A", "B", "C", "Martinez & Chuanez dorez toro", "E", "F", "G", "H", "I", "J"].map((text) => (
                            <Btn 
                                key={text}
                                text={text} 
                                className={`${btnColors[text] || 'bg-white'} text-charcoal py-4 text-base`} 
                                onClick={() => handleButtonClick(text)} 
                            />
                        ))}
                    </div>
                </div>
                <div className="mt-6">
                    <Btn 
                        text="Pick a bet!" 
                        className={`
                            ${isAnySelected ? 'bg-vibrantCoral cursor-pointer' : 'bg-coolGray cursor-not-allowed'}
                            text-white w-full py-4 text-lg font-bold
                        `} 
                        onClick={isAnySelected ? () => alert("Bet placed!") : undefined}
                        disabled={!isAnySelected}
                    />
                </div>
            </div>
        </div>
    );
};

export default ChallengeModal;