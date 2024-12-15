import React, { FC, useState } from 'react';

type ComponentNameProps = {
    title: string;
    description: string;
};

const ComponentName: FC<ComponentNameProps> = ({ title, description }) => {
    const [isHovered, setIsHovered] = useState(false);

    return (
        <div
            className="group relative w-32 h-40 mx-2 my-1 cursor-pointer [perspective:1000px] my-1 shadow-md"
            onMouseEnter={() => setIsHovered(true)}
            onMouseLeave={() => setIsHovered(false)}
        >
            <div
                className={`absolute inset-0 h-full w-full rounded-xl shadow-xl transition-all duration-500 [transform-style:preserve-3d] group-hover:[transform:rotateY(180deg)]`}
            >
                {/* Front face (image and title) */}
                <div
                    className={`absolute inset-0 h-full w-full rounded-xl bg-gray-50 p-1 [backface-visibility:hidden] transition-colors duration-500 ${
                        isHovered ? 'bg-white' : 'bg-white'
                    }`}
                >
                    <div className="rounded-lg bg-gray-100 min-w-28 min-h-28 flex items-center justify-center">
                        <p>100x100</p>
                    </div>
                    <div className="flex items-center justify-center h-12 w-full">
                        <h1 className="font-alexandria font-bold text-sm text-center leading-4 uppercase">
                            {title}
                        </h1>
                    </div>
                </div>

                {/* Back face (description) */}
                <div
                    className={`absolute inset-0 h-full w-full rounded-xl bg-emerald-300 text-black flex items-center justify-center text-left p-2 [transform:rotateY(180deg)] [backface-visibility:hidden] ${
                        isHovered ? 'opacity-100' : 'opacity-100'
                    }`}
                >
                    <p className="font-sans text-sm leading-5 font-bold">{description}</p>
                </div>
            </div>
        </div>
    );
};

export default ComponentName;