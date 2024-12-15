export interface Challenge {
    id: string;
    title: string;
    duration: number;
    isCompleted: boolean;
    description: string;
    guide: string;
}

export const challenges: Challenge[] = [
    { id: '1', title: 'Challenge 1', duration: 27, isCompleted: false, description: 'Complete 5 tasks in a week.', guide: 'Focus on prioritizing important tasks each day.' },
    { id: '2', title: 'Challenge 2', duration: 0, isCompleted: true, description: 'Read a book.', guide: 'Dedicate 30 minutes daily to reading.' },
    { id: '3', title: 'Challenge 3', duration: 12, isCompleted: false, description: 'Run 5 km.', guide: 'Start with short distances and increase gradually.' },
    { id: '4', title: 'Challenge 4', duration: 0, isCompleted: true, description: 'Meditate for 10 days.', guide: 'Find a quiet place and focus on breathing for 10 minutes daily.' },
    { id: '5', title: 'Challenge 5', duration: 48, isCompleted: false, description: 'Learn 10 new words.', guide: 'Use flashcards to memorize and review daily.' },
    { id: '6', title: 'Challenge 6', duration: 0, isCompleted: false, description: 'Cook 3 new recipes.', guide: 'Experiment with cuisines and follow recipes carefully.' },
    { id: '7', title: 'Challenge 7', duration: 5, isCompleted: false, description: 'Drink 2 liters of water daily.', guide: 'Track your intake using a water bottle with markings.' },
    { id: '8', title: 'Challenge 8', duration: 15, isCompleted: true, description: 'Practice yoga.', guide: 'Follow beginner tutorials and practice regularly.' },
    { id: '9', title: 'Challenge 9', duration: 0, isCompleted: false, description: 'Write a short story.', guide: 'Outline the plot first and write a bit daily.' },
    { id: '10', title: 'Challenge 10', duration: 30, isCompleted: true, description: 'Learn a new skill.', guide: 'Dedicate 1 hour daily and follow tutorials.' },
    { id: '11', title: 'Challenge 11', duration: 20, isCompleted: false, description: 'Complete a puzzle.', guide: 'Start with small sections and work systematically.' },
    { id: '12', title: 'Challenge 12', duration: 0, isCompleted: true, description: 'Walk 10,000 steps daily.', guide: 'Track steps with a pedometer and plan your walks.' },
    { id: '13', title: 'Challenge 13', duration: 8, isCompleted: false, description: 'Declutter your home.', guide: 'Focus on one area per day and sort items.' },
    { id: '14', title: 'Challenge 14', duration: 14, isCompleted: false, description: 'Learn basic programming.', guide: 'Follow beginner lessons and practice daily.' },
    { id: '15', title: 'Challenge 15', duration: 0, isCompleted: true, description: 'Start a journal.', guide: 'Write about your thoughts and experiences daily.' },
    { id: '16', title: 'Challenge 16', duration: 25, isCompleted: false, description: 'Plant a garden.', guide: 'Choose easy-to-grow plants and water regularly.' },
    { id: '17', title: 'Challenge 17', duration: 18, isCompleted: true, description: 'Learn a new song on guitar.', guide: 'Practice chords and play along with tutorials.' },
    { id: '18', title: 'Challenge 18', duration: 0, isCompleted: false, description: 'Organize your photos.', guide: 'Create albums and categorize your pictures.' }
];