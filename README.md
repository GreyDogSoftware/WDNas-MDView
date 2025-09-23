![md-view](data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFAAAABQCAYAAACOEfKtAAAACXBIWXMAAAsTAAALEwEAmpwYAAADoElEQVR4nO2b+0/TUBTH9ycp+g8ZcMV/AFxrAnolPsc6lYhSkIeIQHRSYAOBwFR8gEAC8hKDIBDeILABA9ZjziBEB3Nt9yhNzyf5/rRye/uhtz27y7HZCIIgCIIgCIIgCIIgCAOw84UXuWsPfHZeDNgdLqC4ToYXtzhB9GZddV/4Rx7ncF3O5sVgrrNKuV78GpgkU6STQTc5ziqFE9xBThAzj+88lJdH4kBt8h6/Ao4Xg9m598/bOEFsyXE+V4yeFDNZDu9E0WvDdZ3/xGP4hJjJgs7sDteWze4oVG6UGD8hZrKgM3Rnw7eL0ZNhJg26I4ESCQQSKJkzJFAigWAagfHwdPbpngj+bTz0jp2qcZMuMLS3D0V1HZon8bC2HXZDeyQQmVtah4KyJtUTKChrhJmFVVCDJQQi/r4x1RPw94+DWiwjUFEUqGzqjjteeeMHCIcVEngaG4FtuFfliznW3UofrG8GQQuWEoiMTM7FHGvoxyxoxXICY5U2b7r6QQ+WFBiKKm3UlizJvFBTC0SwTMFyRUvJYkmBs0trMT/z94//t2TpGZ5M2YWaRuAjXJ57+6d+huVKrJJleX0Tbj1rJoFMxwsiHA5DacO7lF6oqQQySYbBiRnVAjt6R1J+oaYTeKfCC6sbgbjH/1pYhZuljQlfaFF9B+zofLPHo+fbZPoFMkmOLEtcnlp2brSMH51q3ydNXwvVMDGzGKkcDBHIJBk6v47GPLbBP5D0pdb2ZRiSxfzK78hKMmQJs6Pg8vw5t3ziuLGp+ZQ9q/rHpiFRNoM74K5pM+4ZyP6K+OItbO+Gjo8JbO9CYXVrygTiXuT0/IpuefhoeerpUnWutAhkkgx1bT3Hx9S0fk76+NHBfxDuBmkFt+Fq23pUnydtApkkQ9/oVOTbRqrGj06Jxw97+weaBLZ8HNJ0jrQKvF3eHEm6BGLq23uTVq4YLpAZNP77ge9JKVcsK5BJcmRjN9FyxdIC71R4YXFtI6FyxdICGW7kvmyH4E5Id7lieYHs6FfAg3BYV7lCAqXDeLsHdZUrSRFIkUkgI4HymQoJlEggkEDJ9AKp0YYl1GjDi1vUoSnravXiBHHTxvGiDxvnjJ4QM1lynFXhbN7djO2uGdj/ii2cRk+Kmard1R3M4l3nDhuuBTGTE9yBHGelQp2bcszkF3sidx7KuyKIl6Jb/jPwlsQWTsNb6x1nM/jM4wSx6fjOIwiCIAiCIAiCIAiCIGzp5Q+9cDGLuGZSMAAAAABJRU5ErkJggg==)

# **MD View**

###### For the WD MyCloud (Glacier) NAS drive

---

#### **What this does?**

It's a simple file viewer for md files (and txt). 

#### **Where the idea came from?**

The idea of this came from the necessity of having a simple local repository of files that I can use to reference stuff. My main use is for sharing simple snippets of code or commands inside my own network. However, for sure, it can be used to host a public simple knowledge base, or even a recipes stash.

#### **Is there any limitation?**

It shouldn't be any. However, since this project is for a specific personal use case. Is quite possible that it would not work for you. Most of this project was made in part as a personal challenge, to remember coding stuff.

#### **What environment is this made for?**

The main idea behind this, is running the entire project as an addon for the WD MyCloud (Glacier) NAS drive, that is running some custom Linux based OS, running PHP 7.3.31. However, I'm trying to take a lot of care with the cross platform compatibility... So, yes. This thing also can run on a Windows machine, running PHP 7.3.31 (hint: The public folder contains all the code needed to run the frontend, there aren't system depencies that can tie you to an OS).

**Development enviroment (Tested):**

- OS: Windows 10 (22H2 10.0.19045.6332)

- Server: nginx 1.28.0 + PHP 7.3.31 (In fcgi mode)

**Testing environment (Tested)**

- OS: Linux 4.14.22 armv7l GNU/Linux

- Server:  Apache/2.4.38 + PHP 7.3.31 (as a module)

#### **External libraries?**

Yes. This project uses a few external libraries.

The first is [md-block](https://github.com/leaverou/md-block). Is the main render for the md files.

The second one is [PrismJS](https://github.com/PrismJS/prism/) as a dependency of md-block, and used for syntax highligting.

Third. [Bootstrap](https://github.com/twbs/bootstrap) for the UI stuff.

Last. [Bootstrap icons](https://github.com/twbs/icons) for some of the UI icons.
