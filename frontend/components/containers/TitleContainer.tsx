import React, { FC, ReactNode } from 'react';

type ComponentNameProps = {
    title: string;
    content: ReactNode;
};

const TitleContainer: FC<ComponentNameProps> = ({ title, content }) => {
    return (
        <div className='flex flex-col w-fit h-fit'>
            <h1 className='font-bebasNeue text-charcoal text-2xl font-bold pb-1'>{title}</h1>
            <div className='flex flex-row flex-wrap justify-center items-center'>
                {content}
            </div>
        </div>
    );
};

export default TitleContainer;