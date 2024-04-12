import SyntaxHighlighter from '@/components/shared/SyntaxHighlighter';

const Example = () => {
  return (
    <SyntaxHighlighter language="js">{`import sortBy from '@/utils/sortBy'

const arr = [
	{
		name: 'Jean Doen',
		email: 'jean.d@gmail.com',
	}
]

const data = arr.sort(sortBy('name', false , (a) =>  a.toUpperCase()))
`}</SyntaxHighlighter>
  );
};

export default Example;
