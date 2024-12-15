const users = [
  {
    id: "1",
    name: "user1", 
    email: "user1@example.com",
    password: "Password123",
    rank: '132',
    rounds: '23'
  }
];

export const getUserByEmail = (email: string) => {
  const found = users.find(user => user.email === email);
  return found;
};