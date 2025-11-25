import { Routes, Route, Navigate } from 'react-router-dom';
import Dashboard from './pages/Dashboard';
import Login from './pages/Login';
import Register from './pages/Register';
import CreditCards from './pages/CreditCards';
import Loans from './pages/Loans';
import Bills from './pages/Bills';
import Goals from './pages/Goals';
import ActionPlan from './pages/ActionPlan';
import Layout from './components/Layout';

function App() {
  // TODO: Implement authentication check
  const isAuthenticated = false;

  if (!isAuthenticated) {
    return (
      <Routes>
        <Route path="/login" element={<Login />} />
        <Route path="/register" element={<Register />} />
        <Route path="*" element={<Navigate to="/login" replace />} />
      </Routes>
    );
  }

  return (
    <Routes>
      <Route element={<Layout />}>
        <Route path="/" element={<Dashboard />} />
        <Route path="/credit-cards" element={<CreditCards />} />
        <Route path="/loans" element={<Loans />} />
        <Route path="/bills" element={<Bills />} />
        <Route path="/goals" element={<Goals />} />
        <Route path="/action-plan" element={<ActionPlan />} />
        <Route path="*" element={<Navigate to="/" replace />} />
      </Route>
    </Routes>
  );
}

export default App;
