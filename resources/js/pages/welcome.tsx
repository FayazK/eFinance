import { Head } from '@inertiajs/react';
import { Typography } from 'antd';

const { Title } = Typography;

export default function Welcome() {
    return (
        <>
            <Head title="App" />

            <div
                style={{
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center',
                    minHeight: '100vh',
                    backgroundColor: '#ffffff',
                }}
            >
                <Title level={1}>App</Title>
            </div>
        </>
    );
}
