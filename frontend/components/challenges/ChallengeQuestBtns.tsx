import React from 'react';
import Btn from '../button/Btn';

const ChallengeQuestBtns = () => {
    return (
        <div className="flex flex-col w-full gap-4 pt-6">
            <div className="flex flex-row w-full gap-4">
                <Btn text="Answer A" className="w-full bg-goldenYellow text-blueBlack" />
                <Btn text="Answer B" className="w-full bg-goldenYellow text-blueBlack" />
            </div>
            <div className="flex flex-row w-full gap-4">
                <Btn text="Answer C" className="w-full bg-goldenYellow text-blueBlack" />
                <Btn text="Answer D" className="w-full bg-goldenYellow text-blueBlack" />
            </div>
        </div>
    );
};

export default ChallengeQuestBtns;