import React from 'react';

interface BackgroundLayoutWrapperProps {
    children: React.ReactNode;
}

const BackgroundLayoutWrapper: React.FC<BackgroundLayoutWrapperProps> = ({ children }) => {
    return (
        <div className="relative min-h-screen w-full">
            <div className="relative z-10 pt-4">
                {children}
            </div>
        </div>
    );
};

export default BackgroundLayoutWrapper;