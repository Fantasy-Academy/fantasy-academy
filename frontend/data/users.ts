// mockData/users.js
const users = [
  {
    id: "1",
    name: "user1", 
    email: "user1@example.com",
    password: "Password123",
  },
  {
    id: "2",
    name: "user2", 
    email: "user2@example.com",
    password: "Password456",
  },
];

export const getUserByEmail = (email: string) => {
  const found = users.find(user => user.email === email);
  return found;
};