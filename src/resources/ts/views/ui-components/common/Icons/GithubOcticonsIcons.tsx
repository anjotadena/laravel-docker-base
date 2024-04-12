import IconWrapper from './IconWrapper';
import {
  // GoOctoface,
  GoFlame,
  // GoMarkGithub,
  GoGitCompare,
  GoGitCommit,
  GoGitPullRequest,
  GoCode,
  // GoMarkdown,
  GoTerminal,
} from 'react-icons/go';

const renderIcon = [
  // { render: () => <GoOctoface /> },
  { render: () => <GoFlame /> },
  // { render: () => <GoMarkGithub /> },
  { render: () => <GoGitCompare /> },
  { render: () => <GoGitCommit /> },
  { render: () => <GoGitPullRequest /> },
  { render: () => <GoCode /> },
  // { render: () => <GoMarkdown /> },
  { render: () => <GoTerminal /> },
];

const GithubOcticonsIcons = () => {
  return (
    <div className="grid grid-cols-3 gap-y-6 text-4xl text-center heading-text">
      {renderIcon.map((icon, index) => (
        <IconWrapper key={`githubOcticonsIcons-${index}`}>
          {icon.render()}
        </IconWrapper>
      ))}
    </div>
  );
};

export default GithubOcticonsIcons;
