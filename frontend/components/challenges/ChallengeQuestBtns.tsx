import React from 'react';
import Btn from '../common/Btn';

const ChallengeQuestBtns = () => {
    return (
        <div className="flex flex-col w-full gap-4 pt-6">
            <div className="flex flex-row w-full gap-4">
                <Btn text="Answer A" className="w-full bg-cyan-400" />
                <Btn text="Answer B" className="w-full bg-cyan-400" />
            </div>
            <div className="flex flex-row w-full gap-4">
                <Btn text="Answer C" className="w-full bg-cyan-400" />
                <Btn text="Answer D" className="w-full bg-cyan-400" />
            </div>
        </div>
    );
};

export default ChallengeQuestBtns;