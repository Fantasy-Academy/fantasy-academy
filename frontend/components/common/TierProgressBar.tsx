'use client';

import React, { useMemo } from 'react';

type Tier = 'Bronze' | 'Silver' | 'Gold' | 'Platinum';

type TierThreshold = { tier: Tier; min: number };

const TIERS: TierThreshold[] = [
  { tier: 'Bronze',   min: 0 },
  { tier: 'Silver',   min: 300 },
  { tier: 'Gold',     min: 600 },
  { tier: 'Platinum', min: 1000 }, // top tier
];

function getCurrentTier(points: number): TierThreshold {
  let current = TIERS[0];
  for (const t of TIERS) if (points >= t.min) current = t;
  return current;
}

function getNextTier(current: TierThreshold): TierThreshold | null {
  const i = TIERS.findIndex(t => t.tier === current.tier);
  return i >= 0 && i < TIERS.length - 1 ? TIERS[i + 1] : null;
}

type TierProgressBarProps = {
  points: number;       // aktuální body
  className?: string;
};

const TierProgressBar: React.FC<TierProgressBarProps> = ({ points, className }) => {
  const { current, next, pct, toNext } = useMemo(() => {
    const p = Math.max(0, points || 0);
    const current = getCurrentTier(p);
    const next = getNextTier(current);

    if (!next) return { current, next: null, pct: 100, toNext: 0 };

    const range = next.min - current.min;
    const gained = p - current.min;
    const pct = Math.max(0, Math.min(100, Math.round((gained / range) * 100)));
    const toNext = Math.max(0, next.min - p);

    return { current, next, pct, toNext };
  }, [points]);

  return (
    <div className={className ?? ''}>
      <div className="flex items-baseline justify-between mb-1">
        <div className="text-sm text-charcoal">
          <span className="font-semibold">{current.tier}</span>
          {next ? <span className="text-coolGray"> → {next.tier}</span> : <span className="text-pistachio ml-2">Max tier reached</span>}
        </div>
        <div className="text-sm text-charcoal">
          {next ? (<><span className="font-medium">{pct}%</span><span className="text-coolGray ml-2">({toNext} pts to {next.tier})</span></>) : (<span className="font-medium">100%</span>)}
        </div>
      </div>

      <div className="h-2 w-full rounded bg-gray-200 overflow-hidden">
        <div
          className="h-full bg-vibrantCoral transition-[width] duration-500 ease-out"
          style={{ width: `${pct}%` }}
          role="progressbar"
          aria-valuenow={pct}
          aria-valuemin={0}
          aria-valuemax={100}
        />
      </div>

      <div className="mt-1 text-xs text-coolGray">
        Points: <span className="text-charcoal font-medium">{Math.max(0, points || 0)}</span>
      </div>
    </div>
  );
};

export default TierProgressBar;