import AdaptableCard from '@/components/shared/AdaptableCard';
import Container from '@/components/shared/Container';
import type { ReactNode } from 'react';

type Log = {
  version: string;
  date: string;
  updateContent: string[];
};

type LogProps = Omit<Log, 'updateContent'> & {
  border?: boolean;
  children?: ReactNode;
};

const logData: Log[] = [
  {
    version: '1.0.0',
    date: '10 Apr 2024',
    updateContent: ['[Release] Initial Release.'],
  },
];

const Log = (props: LogProps) => {
  return (
    <div className={`py-4 ${props.border && 'border-bottom'}`}>
      <div className="flex items-center">
        <h5 className="font-weight-normal mb-0 mr-3">{props.version}</h5>
        <code>{props.date}</code>
      </div>
      <div className="api-container p-0 border-0 mt-3">{props.children}</div>
    </div>
  );
};

const Changelog = () => {
  return (
    <Container>
      <AdaptableCard>
        <h4>Changelog</h4>
        {logData.map((elm) => (
          <Log key={elm.version} version={`v${elm.version}`} date={elm.date}>
            {elm.updateContent.length > 0 ? (
              <ul>
                {elm.updateContent.map((item, i) => (
                  <li key={i}>- {item}</li>
                ))}
              </ul>
            ) : null}
          </Log>
        ))}
      </AdaptableCard>
    </Container>
  );
};

export default Changelog;
