import React from 'react';
import { labelHandler, LabelHandlerProps } from './labelHandler';

interface TimeLabelProps extends LabelHandlerProps { }

const TimeLabel: React.FC<TimeLabelProps> = ({ duration, isCompleted }) => {
    const label = labelHandler({ duration, isCompleted });

    const labelClasses = (() => {
        if (typeof label === 'number') {
            return 'text-vibrantCoral';
        } else if (label === 'COMPLETED') {
            return 'text-pistachio';
        } else if (label === "TIME'S UP") {
            return 'text-white';
        }
        return '';
    })();

    return (
        <div className={`px-4 py-2 rounded text-center ${labelClasses}`}>
            <p className='font-bebasNeue font-normal not-italic text-2xl'>
                {label}
                {typeof label === 'number' && label > 0 ? ' h' : ''}
            </p>
        </div>
    );
};

export default TimeLabel;