import Dropdown from '@/components/ui/Dropdown';
import Button from '@/components/ui/Button';

const CustomToggle = () => {
  return (
    <div>
      <Dropdown renderTitle={<Button>Toggle as Button</Button>}>
        <Dropdown.Item eventKey="a">Item A</Dropdown.Item>
        <Dropdown.Item eventKey="b">Item B</Dropdown.Item>
        <Dropdown.Item eventKey="c">Item C</Dropdown.Item>
        <Dropdown.Item eventKey="d">Item D</Dropdown.Item>
      </Dropdown>
    </div>
  );
};

export default CustomToggle;
