import { HttpService } from '@nestjs/axios';
import { Injectable } from '@nestjs/common';
import { AxiosRequestHeaders } from 'axios';
import { KeyValueService } from 'src/keyValue/keyValue.service';


const WAZE_LOGIN_URL = 'https://www.waze.com/login/get'
const URL =
  'https://www.waze.com/row-Descartes/app/Features?bbox=31.983921%2C36.543022%2C32.041885%2C36.558934&language=ru&v=2&problemFilter=0%2C1&roadClosures=true&cameras=true&mapComments=true&restrictedDrivingAreas=true&railroadCrossings=true&roadTypes=1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C15%2C16%2C17%2C18%2C19%2C20%2C22&venueLevel=3&venueFilter=3%2C0%2C2';

@Injectable()
export class CheckerService {
  constructor(
    private readonly httpService: HttpService,
    private readonly keyValueService: KeyValueService,
  ) {}

  async checkLogin() {
    const res = await this.wazeRequest(WAZE_LOGIN_URL)

    return res.reply
  }


  async setToken(token: string) {
    await this.keyValueService.setValue('SESSION_COOKIE', token);

    return this.checkLogin()
  }


  async startCheck(token: string) {
    const res = await this.httpService.axiosRef.get(URL, {
      headers: {
        cookie: `_web_session=${token}`,
      },
    });

  }

  private async wazeRequest(url: string, data?: AxiosRequestHeaders) {
    const keyValue = await this.keyValueService.findByKey('SESSION_COOKIE');

    const res = await this.httpService.axiosRef.get(url, {
      headers: {
        cookie: `_web_session=${keyValue.value}`,
      },
      ...data
    });

    await this.setCookie(res.headers)

    return res.data
  }

  private async setCookie(headers) {
    const cookies = headers['set-cookie']

    const [webSessionCookie, ...rest] = cookies.find(cookie => cookie.startsWith('_web_session')).split('; ')
    const [_ ,token] = webSessionCookie.split('=')

    await this.keyValueService.setValue('SESSION_COOKIE', token);

  }
}
