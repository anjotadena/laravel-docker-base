import SyntaxHighlighter from '@/components/shared/SyntaxHighlighter';

const Example = () => {
  return (
    <SyntaxHighlighter language="js">{`import wildCardSearch from '@/utils/wildCardSearch'

const arr = [
	{
		name: 'Carolyn Perkins',
		email: 'eileen_h@gmail.com',
	},
	{
		name: 'Terrance Moreno',
		email: 'terrance_moreno@lithos.io',
	},
	{
		name: 'Ron Vargas',
		email: 'ronnie_vergas@lithos.io',
	},
	{
		name: 'Luke Cook',
		email: 'cookie_lukie@gmail.com',
	},
]

const data = wildCardSearch(arr, 'Terran')

// output: [
//  {
// 		name: 'Terrance Moreno',
// 		email: 'terrance_moreno@lithos.io',
// 	},
// ]
`}</SyntaxHighlighter>
  );
};

export default Example;
