# SUITERUS TECHNOLOGIES INC.`s FRONTEND TEMPLATE

This git repo is exclusive for the employees of Suiterus Technologies INC. only.

## SYSTEM REQUIREMENTS
<ul>
    <li>
        <p>
            Nodejs 16.5.0 or latest.<br>
            Download link(Windows OS 64 Bit only) : <a href="https://nodejs.org/download/release/v16.5.0/node-v16.5.0-x64.msi">https://nodejs.org/download/release/v16.5.0/node-v16.5.0-x64.msi</a>
        </p>
    </li>
    <li>
        <p>
            Python 3.10 or latest.<br>
            Download link(Windows OS 64 Bit only) : <a href="https://www.python.org/ftp/python/3.10.0/python-3.10.0-amd64.exe">https://www.python.org/ftp/python/3.10.0/python-3.10.0-amd64.exe</a>
        </p>
    </li>
    <li>
        <p>
            (For windows only)Visual Studio C++ 2017 and Windows Build Tools.<br>
            Run the following commands on powershell as admin :<br>
            #: npm install --global windows-build-tools
        </p>
    </li>
</ul>

## INSTALLATION
```
git clone <repo_url> '<folder_name_here>
cd <folder_name_here>
npm install

# copy .env.example file
cp .env.example .env
# or
copy .env.example .env

# edit .env file
APP_URL="localhost:8000" #you can also put here your api domain/url.
CLIENT_URL="localhost:3000"
```

## USAGE
Run in Development Mode
```
npm run dev
# visit http://localhost:1027 afer
```
Run in Production Mode
```
npm run build
npm run start
# visit http://localhost:1027 afer
```

# Demo link
<a href="https://frontend.template.demo.suiterus.com/" target="_blank">Click here to see the demo</a>

<br><br>
<p>
    Created by Roldhan Dasalla
</p>
<p>
    Dependecy template : <a href="https://github.com/cretueusebiu/laravel-nuxt" target="_blank">https://github.com/cretueusebiu/laravel-nuxt</a>
</p>

# Change logs

## UPDATE 4.0
<ul>
    <li>
        <p>
            Updated package.json to support latest nodejs and npm versions.</a>
        </p>
    </li>
</ul>
