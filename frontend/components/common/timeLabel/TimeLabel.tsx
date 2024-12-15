import React from 'react';
import { labelHandler, LabelHandlerProps } from './labelHandler';

interface TimeLabelProps extends LabelHandlerProps { }

const TimeLabel: React.FC<TimeLabelProps> = ({ duration, isCompleted }) => {
    const label = labelHandler({ duration, isCompleted });

    const labelClasses = (() => {
        if (typeof label === 'number') {
            return 'text-black bg-white';
        } else if (label === 'COMPLETED') {
            return 'text-black bg-emerald-300';
        } else if (label === 'EXPIRED') {
            return 'text-white bg-red-500';
        }
        return '';
    })();

    return (
        <div className={`px-2 py-1 rounded text-center ${labelClasses}`}>
            <p className='font-medium'>
                {label}
                {typeof label === 'number' && label > 0 ? ' h' : ''}
            </p>
        </div>
    );
};

export default TimeLabel;