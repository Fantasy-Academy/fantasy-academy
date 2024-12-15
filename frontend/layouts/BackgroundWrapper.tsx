import React from 'react';
import BgStripe from './BgStripe';

interface BackgroundLayoutWrapperProps {
    children: React.ReactNode;
}

const BackgroundLayoutWrapper: React.FC<BackgroundLayoutWrapperProps> = ({ children }) => {
    return (
        <div className="relative min-h-screen w-full">
            <div className="absolute top-0 left-0 right-0 bottom-0 z-[-1] overflow-hidden">
                <BgStripe />
            </div>
            <div className="relative z-10 pt-4">
                {children}
            </div>
        </div>
    );
};

export default BackgroundLayoutWrapper;